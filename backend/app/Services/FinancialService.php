<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\TaxConfiguration;
use App\Models\Account;
use App\Models\FinancialTransaction;
use App\Models\FinancialEntry;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialService
{
    /**
     * Calculate taxes for an amount
     */
    public function calculateTaxes(float $amount, int $propertyId, ?Carbon $date = null): array
    {
        $date = $date ?? now();
        
        $taxes = TaxConfiguration::where('property_id', $propertyId)
            ->where('is_active', true)
            ->where(function($q) use ($date) {
                $q->whereNull('effective_from')
                  ->orWhere('effective_from', '<=', $date);
            })
            ->where(function($q) use ($date) {
                $q->whereNull('effective_to')
                  ->orWhere('effective_to', '>=', $date);
            })
            ->orderBy('display_order')
            ->get();

        $taxBreakdown = [];
        $totalTax = 0;
        $taxableAmount = $amount;

        foreach ($taxes as $tax) {
            $taxAmount = 0;
            
            if ($tax->calculation_type === 'percentage') {
                $taxAmount = ($taxableAmount * $tax->rate) / 100;
            } else {
                $taxAmount = $tax->fixed_amount_cents / 100;
            }

            $taxBreakdown[] = [
                'name' => $tax->name,
                'code' => $tax->code,
                'rate' => $tax->rate,
                'amount' => $taxAmount,
                'is_compound' => $tax->is_compound
            ];

            $totalTax += $taxAmount;
            
            // If compound tax, next tax is calculated on amount + this tax
            if ($tax->is_compound) {
                $taxableAmount += $taxAmount;
            }
        }

        return [
            'subtotal' => $amount,
            'tax_breakdown' => $taxBreakdown,
            'total_tax' => $totalTax,
            'total' => $amount + $totalTax
        ];
    }

    /**
     * Create journal entry for invoice
     */
    public function createInvoiceJournalEntry(Invoice $invoice): FinancialTransaction
    {
        // Get accounts
        $accountsReceivable = Account::where('property_id', $invoice->property_id)
            ->where('code', 'AR-001') // Accounts Receivable
            ->first();
            
        $revenueAccount = Account::where('property_id', $invoice->property_id)
            ->where('code', 'REV-001') // Revenue
            ->first();
            
        $taxPayableAccount = Account::where('property_id', $invoice->property_id)
            ->where('code', 'TAX-001') // Tax Payable
            ->first();

        if (!$accountsReceivable || !$revenueAccount || !$taxPayableAccount) {
            throw new \Exception('Required accounts not found. Please setup chart of accounts.');
        }

        DB::beginTransaction();
        try {
            $transaction = FinancialTransaction::create([
                'property_id' => $invoice->property_id,
                'transaction_number' => $this->generateTransactionNumber($invoice->property_id, 'JE'),
                'reference_type' => 'App\Models\Invoice',
                'reference_id' => $invoice->id,
                'type' => 'journal',
                'transaction_date' => $invoice->issue_date,
                'description' => "Invoice {$invoice->invoice_number} - {$invoice->guest->full_name}",
                'total_debit_cents' => $invoice->total_cents,
                'total_credit_cents' => $invoice->total_cents,
                'status' => 'posted',
                'posted_at' => now(),
                'created_by_user_id' => auth()->id()
            ]);

            // Debit: Accounts Receivable (Asset increases)
            FinancialEntry::create([
                'financial_transaction_id' => $transaction->id,
                'account_id' => $accountsReceivable->id,
                'debit_cents' => $invoice->total_cents,
                'credit_cents' => 0,
                'narration' => "Invoice {$invoice->invoice_number}",
                'entry_order' => 1
            ]);

            // Credit: Revenue (Revenue increases)
            FinancialEntry::create([
                'financial_transaction_id' => $transaction->id,
                'account_id' => $revenueAccount->id,
                'debit_cents' => 0,
                'credit_cents' => $invoice->subtotal_cents - $invoice->discount_cents,
                'narration' => "Revenue from Invoice {$invoice->invoice_number}",
                'entry_order' => 2
            ]);

            // Credit: Tax Payable (Liability increases)
            if ($invoice->tax_cents > 0) {
                FinancialEntry::create([
                    'financial_transaction_id' => $transaction->id,
                    'account_id' => $taxPayableAccount->id,
                    'debit_cents' => 0,
                    'credit_cents' => $invoice->tax_cents,
                    'narration' => "Tax on Invoice {$invoice->invoice_number}",
                    'entry_order' => 3
                ]);
            }

            DB::commit();
            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Create journal entry for payment
     */
    public function createPaymentJournalEntry(Payment $payment): FinancialTransaction
    {
        // Get accounts based on payment method
        $cashBankAccount = $this->getCashBankAccount($payment->property_id, $payment->method);
        
        $accountsReceivable = Account::where('property_id', $payment->property_id)
            ->where('code', 'AR-001')
            ->first();

        if (!$cashBankAccount || !$accountsReceivable) {
            throw new \Exception('Required accounts not found');
        }

        DB::beginTransaction();
        try {
            $transaction = FinancialTransaction::create([
                'property_id' => $payment->property_id,
                'transaction_number' => $this->generateTransactionNumber($payment->property_id, 'RE'),
                'reference_type' => 'App\Models\Payment',
                'reference_id' => $payment->id,
                'type' => 'receipt',
                'transaction_date' => $payment->paid_at ?? now(),
                'description' => "Payment {$payment->payment_reference} - {$payment->method}",
                'total_debit_cents' => $payment->amount_cents,
                'total_credit_cents' => $payment->amount_cents,
                'status' => 'posted',
                'posted_at' => now(),
                'created_by_user_id' => auth()->id()
            ]);

            // Debit: Cash/Bank (Asset increases)
            FinancialEntry::create([
                'financial_transaction_id' => $transaction->id,
                'account_id' => $cashBankAccount->id,
                'debit_cents' => $payment->amount_cents,
                'credit_cents' => 0,
                'narration' => "Payment received via {$payment->method}",
                'entry_order' => 1
            ]);

            // Credit: Accounts Receivable (Asset decreases)
            FinancialEntry::create([
                'financial_transaction_id' => $transaction->id,
                'account_id' => $accountsReceivable->id,
                'debit_cents' => 0,
                'credit_cents' => $payment->amount_cents,
                'narration' => "Payment {$payment->payment_reference}",
                'entry_order' => 2
            ]);

            DB::commit();
            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Create journal entry for refund
     */
    public function createRefundJournalEntry(Refund $refund): FinancialTransaction
    {
        $cashBankAccount = $this->getCashBankAccount($refund->property_id, $refund->method);
        
        $accountsReceivable = Account::where('property_id', $refund->property_id)
            ->where('code', 'AR-001')
            ->first();

        if (!$cashBankAccount || !$accountsReceivable) {
            throw new \Exception('Required accounts not found');
        }

        DB::beginTransaction();
        try {
            $transaction = FinancialTransaction::create([
                'property_id' => $refund->property_id,
                'transaction_number' => $this->generateTransactionNumber($refund->property_id, 'RF'),
                'reference_type' => 'App\Models\Refund',
                'reference_id' => $refund->id,
                'type' => 'payment',
                'transaction_date' => $refund->completed_at ?? now(),
                'description' => "Refund {$refund->refund_reference} - {$refund->reason}",
                'total_debit_cents' => $refund->amount_cents,
                'total_credit_cents' => $refund->amount_cents,
                'status' => 'posted',
                'posted_at' => now(),
                'created_by_user_id' => auth()->id()
            ]);

            // Debit: Accounts Receivable (Asset increases - negative receivable)
            FinancialEntry::create([
                'financial_transaction_id' => $transaction->id,
                'account_id' => $accountsReceivable->id,
                'debit_cents' => $refund->amount_cents,
                'credit_cents' => 0,
                'narration' => "Refund {$refund->refund_reference}",
                'entry_order' => 1
            ]);

            // Credit: Cash/Bank (Asset decreases)
            FinancialEntry::create([
                'financial_transaction_id' => $transaction->id,
                'account_id' => $cashBankAccount->id,
                'debit_cents' => 0,
                'credit_cents' => $refund->amount_cents,
                'narration' => "Refund paid via {$refund->method}",
                'entry_order' => 2
            ]);

            DB::commit();
            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get cash/bank account based on payment method
     */
    protected function getCashBankAccount(int $propertyId, string $method): ?Account
    {
        $accountCodes = [
            'cash' => 'CASH-001',
            'card' => 'BANK-001',
            'upi' => 'BANK-001',
            'net_banking' => 'BANK-001',
            'cheque' => 'BANK-001',
            'wallet' => 'WALLET-001',
            'bank_transfer' => 'BANK-001',
            'other' => 'CASH-001'
        ];

        $code = $accountCodes[$method] ?? 'CASH-001';

        return Account::where('property_id', $propertyId)
            ->where('code', $code)
            ->first();
    }

    /**
     * Generate transaction number
     */
    protected function generateTransactionNumber(int $propertyId, string $prefix): string
    {
        $year = date('Y');
        $month = date('m');
        
        $lastTransaction = FinancialTransaction::where('property_id', $propertyId)
            ->where('transaction_number', 'like', "{$prefix}-{$year}{$month}-%")
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastTransaction) {
            $lastNumber = (int) substr($lastTransaction->transaction_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return sprintf('%s-%s%s-%04d', $prefix, $year, $month, $newNumber);
    }

    /**
     * Calculate aging buckets for invoices
     */
    public function calculateAgingReport(int $propertyId, ?Carbon $asOfDate = null): array
    {
        $asOfDate = $asOfDate ?? now();
        
        $invoices = Invoice::where('property_id', $propertyId)
            ->where('balance_cents', '>', 0)
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->get();

        $aging = [
            'current' => 0, // 0-30 days
            '31_60' => 0,    // 31-60 days
            '61_90' => 0,    // 61-90 days
            '91_120' => 0,   // 91-120 days
            'over_120' => 0  // Over 120 days
        ];

        foreach ($invoices as $invoice) {
            if (!$invoice->due_date) {
                continue;
            }

            $daysOverdue = $invoice->due_date->diffInDays($asOfDate, false);
            $balance = $invoice->balance;

            if ($daysOverdue <= 0) {
                $aging['current'] += $balance;
            } elseif ($daysOverdue <= 30) {
                $aging['current'] += $balance;
            } elseif ($daysOverdue <= 60) {
                $aging['31_60'] += $balance;
            } elseif ($daysOverdue <= 90) {
                $aging['61_90'] += $balance;
            } elseif ($daysOverdue <= 120) {
                $aging['91_120'] += $balance;
            } else {
                $aging['over_120'] += $balance;
            }
        }

        return $aging;
    }

    /**
     * Get revenue analysis
     */
    public function getRevenueAnalysis(int $propertyId, Carbon $startDate, Carbon $endDate): array
    {
        $invoices = Invoice::where('property_id', $propertyId)
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->whereNotIn('status', ['cancelled'])
            ->get();

        $analysis = [
            'total_invoiced' => $invoices->sum('total'),
            'total_paid' => $invoices->sum('paid'),
            'outstanding' => $invoices->sum('balance'),
            'invoice_count' => $invoices->count(),
            'average_invoice_value' => $invoices->avg('total'),
            'by_type' => [],
            'daily_revenue' => []
        ];

        // Group by type
        $byType = $invoices->groupBy('type');
        foreach ($byType as $type => $items) {
            $analysis['by_type'][$type] = [
                'count' => $items->count(),
                'total' => $items->sum('total'),
                'paid' => $items->sum('paid'),
                'outstanding' => $items->sum('balance')
            ];
        }

        // Daily revenue
        $dailyRevenue = $invoices->groupBy(function($invoice) {
            return $invoice->issue_date->format('Y-m-d');
        });

        foreach ($dailyRevenue as $date => $items) {
            $analysis['daily_revenue'][$date] = $items->sum('total');
        }

        return $analysis;
    }

    /**
     * Get payment summary
     */
    public function getPaymentSummary(int $propertyId, Carbon $startDate, Carbon $endDate): array
    {
        $payments = Payment::where('property_id', $propertyId)
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get();

        $summary = [
            'total_received' => $payments->sum('amount'),
            'payment_count' => $payments->count(),
            'average_payment' => $payments->avg('amount'),
            'by_method' => []
        ];

        // Group by method
        $byMethod = $payments->groupBy('method');
        foreach ($byMethod as $method => $items) {
            $summary['by_method'][$method] = [
                'count' => $items->count(),
                'total' => $items->sum('amount')
            ];
        }

        return $summary;
    }

    /**
     * Setup default chart of accounts for a property
     */
    public function setupDefaultAccounts(int $propertyId): void
    {
        $accounts = [
            // Assets
            ['code' => 'CASH-001', 'name' => 'Cash on Hand', 'type' => 'asset', 'sub_type' => 'current_asset'],
            ['code' => 'BANK-001', 'name' => 'Bank Account', 'type' => 'asset', 'sub_type' => 'current_asset'],
            ['code' => 'AR-001', 'name' => 'Accounts Receivable', 'type' => 'asset', 'sub_type' => 'current_asset'],
            ['code' => 'WALLET-001', 'name' => 'Digital Wallet', 'type' => 'asset', 'sub_type' => 'current_asset'],
            
            // Liabilities
            ['code' => 'AP-001', 'name' => 'Accounts Payable', 'type' => 'liability', 'sub_type' => 'current_liability'],
            ['code' => 'TAX-001', 'name' => 'Tax Payable', 'type' => 'liability', 'sub_type' => 'current_liability'],
            ['code' => 'DEP-001', 'name' => 'Customer Deposits', 'type' => 'liability', 'sub_type' => 'current_liability'],
            
            // Equity
            ['code' => 'EQ-001', 'name' => 'Owner\'s Equity', 'type' => 'equity', 'sub_type' => 'owner_equity'],
            ['code' => 'RE-001', 'name' => 'Retained Earnings', 'type' => 'equity', 'sub_type' => 'retained_earnings'],
            
            // Revenue
            ['code' => 'REV-001', 'name' => 'Room Revenue', 'type' => 'revenue', 'sub_type' => 'operating_revenue'],
            ['code' => 'REV-002', 'name' => 'Food & Beverage Revenue', 'type' => 'revenue', 'sub_type' => 'operating_revenue'],
            ['code' => 'REV-003', 'name' => 'Service Revenue', 'type' => 'revenue', 'sub_type' => 'operating_revenue'],
            ['code' => 'REV-999', 'name' => 'Other Revenue', 'type' => 'revenue', 'sub_type' => 'other_revenue'],
            
            // Expenses
            ['code' => 'EXP-001', 'name' => 'Salaries & Wages', 'type' => 'expense', 'sub_type' => 'operating_expense'],
            ['code' => 'EXP-002', 'name' => 'Utilities', 'type' => 'expense', 'sub_type' => 'operating_expense'],
            ['code' => 'EXP-003', 'name' => 'Maintenance', 'type' => 'expense', 'sub_type' => 'operating_expense'],
            ['code' => 'EXP-004', 'name' => 'Supplies', 'type' => 'expense', 'sub_type' => 'operating_expense'],
            ['code' => 'EXP-005', 'name' => 'Marketing', 'type' => 'expense', 'sub_type' => 'operating_expense'],
        ];

        DB::beginTransaction();
        try {
            foreach ($accounts as $index => $accountData) {
                Account::create(array_merge($accountData, [
                    'property_id' => $propertyId,
                    'is_system' => true,
                    'is_active' => true,
                    'display_order' => $index + 1
                ]));
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Validate payment allocation
     */
    public function validatePaymentAllocation(Payment $payment, Invoice $invoice, float $amount): bool
    {
        // Check if payment has sufficient unallocated amount
        if ($amount > $payment->unallocated_amount) {
            throw new \Exception('Payment has insufficient unallocated amount');
        }

        // Check if invoice has sufficient balance
        if ($amount > $invoice->balance) {
            throw new \Exception('Amount exceeds invoice balance');
        }

        // Check if invoice is in valid status
        if (!in_array($invoice->status, ['draft', 'pending', 'partially_paid', 'overdue'])) {
            throw new \Exception('Invoice is not in valid status for payment allocation');
        }

        return true;
    }

    /**
     * Auto-reconcile payments with invoices
     */
    public function autoReconcile(int $propertyId, ?int $guestId = null): array
    {
        $results = [];

        // Get unallocated payments
        $payments = Payment::where('property_id', $propertyId)
            ->where('status', 'completed')
            ->get()
            ->filter(function($payment) {
                return $payment->unallocated_amount > 0;
            });

        if ($guestId) {
            $payments = $payments->where('guest_id', $guestId);
        }

        DB::beginTransaction();
        try {
            foreach ($payments as $payment) {
                $allocations = $payment->autoAllocate();
                $results[] = [
                    'payment_id' => $payment->id,
                    'payment_reference' => $payment->payment_reference,
                    'allocations_count' => count($allocations),
                    'allocated_amount' => $payment->amount - $payment->unallocated_amount
                ];
            }

            DB::commit();
            return $results;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}