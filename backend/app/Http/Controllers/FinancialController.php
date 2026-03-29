<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceLineItem;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\Booking;
use App\Models\Guest;
use App\Models\Property;
use App\Services\Breadcrumbs;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\PaymentAllocation;

class FinancialController extends Controller
{
    // ============================================
    // INVOICE MANAGEMENT
    // ============================================

    /**
     * Display invoices listing
     */
    public function invoiceIndex()
    {
        Breadcrumbs::add('Financial Management');
        Breadcrumbs::add('Invoices');
        
        $properties = Property::all();
        $guests = Guest::all();
        return view('financials.invoices.index', compact('properties', 'guests'));
    }

    /**
     * Get invoices data for DataTables
     */
    public function invoiceAjax(Request $request)
    {
        $query = Invoice::with(['property', 'guest', 'booking'])
            ->select(['invoices.*'])
            ->orderBy('issue_date', 'desc');

        // Apply filters
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('guest_id')) {
            $query->where('guest_id', $request->guest_id);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('invoice_number_display', function ($row) {
                $badge = '';
                if ($row->is_overdue) {
                    $badge = '<span class="badge bg-danger ms-1">Overdue</span>';
                }
                return '<div class="fw-bold">' . $row->invoice_number . $badge . '</div>' .
                       '<small class="text-muted">' . ucfirst($row->type) . '</small>';
            })
            ->addColumn('guest_display', function ($row) {
                if ($row->guest) {
                    return '<div class="fw-bold">' . $row->guest->full_name . '</div>' .
                           '<small>' . $row->guest->phone . '</small>';
                }
                return '<span class="text-muted">-</span>';
            })
            ->addColumn('dates_display', function ($row) {
                return '<div><strong>Issue:</strong> ' . $row->issue_date->format('d M Y') . '</div>' .
                       '<div><strong>Due:</strong> ' . ($row->due_date ? $row->due_date->format('d M Y') : 'N/A') . '</div>';
            })
            ->addColumn('amount_display', function ($row) {
                return '<div class="text-end">' .
                       '<div class="fw-bold">₹' . number_format($row->total, 2) . '</div>' .
                       '<small class="text-success">Paid: ₹' . number_format($row->paid, 2) . '</small><br>' .
                       '<small class="text-danger">Balance: ₹' . number_format($row->balance, 2) . '</small>' .
                       '</div>';
            })
            ->addColumn('status_badge', function ($row) {
                $colors = [
                    'draft' => 'secondary',
                    'pending' => 'warning',
                    'paid' => 'success',
                    'partially_paid' => 'info',
                    'overdue' => 'danger',
                    'cancelled' => 'dark',
                    'refunded' => 'purple'
                ];
                $color = $colors[$row->status] ?? 'secondary';
                return '<span class="badge bg-'.$color.'">'.ucfirst(str_replace('_', ' ', $row->status)).'</span>';
            })
            ->addColumn('action', function ($row) {
                $actions = '
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item view-invoice" href="#" data-id="'.$row->id.'">
                                <i class="bx bx-show"></i> View</a></li>';
                
                if ($row->canEdit()) {
                    $actions .= '<li><a class="dropdown-item edit-invoice" href="#" data-id="'.$row->id.'">
                                <i class="bx bx-edit"></i> Edit</a></li>';
                }
                
                $actions .= '<li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="'.route('financials.invoices.pdf', $row->id).'" target="_blank">
                                <i class="bx bx-download"></i> Download PDF</a></li>
                            <li><a class="dropdown-item send-invoice" href="#" data-id="'.$row->id.'">
                                <i class="bx bx-send"></i> Send Email</a></li>
                            <li><hr class="dropdown-divider"></li>';
                
                if ($row->balance > 0 && $row->status !== 'cancelled') {
                    $actions .= '<li><a class="dropdown-item add-payment-to-invoice" href="#" data-id="'.$row->id.'">
                                <i class="bx bx-wallet"></i> Add Payment</a></li>';
                }
                
                if ($row->canEdit()) {
                    $actions .= '<li><a class="dropdown-item cancel-invoice" href="#" data-id="'.$row->id.'">
                                <i class="bx bx-x"></i> Cancel</a></li>';
                }
                
                if ($row->canDelete()) {
                    $actions .= '<li><a class="dropdown-item delete-invoice" href="#" data-id="'.$row->id.'">
                                <i class="bx bx-trash"></i> Delete</a></li>';
                }
                
                $actions .= '</ul></div>';
                
                return $actions;
            })
            ->rawColumns(['invoice_number_display', 'guest_display', 'dates_display', 'amount_display', 'status_badge', 'action'])
            ->make(true);
    }

    /**
     * Show invoice details
     */
    public function invoiceShow($id)
    {
        $invoice = Invoice::with([
            'property',
            'guest',
            'booking',
            'lineItems.reference',
            'paymentAllocations.payment',
            'createdBy'
        ])->findOrFail($id);

        return response()->json($invoice);
    }

    /**
     * Store new invoice
     */
    public function invoiceStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'property_id' => 'required|exists:properties,id',
            'booking_id' => 'nullable|exists:bookings,id',
            'guest_id' => 'required|exists:guests,id',
            'type' => 'required|in:booking,folio,proforma,credit_note,debit_note',
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:issue_date',
            'line_items' => 'required|array|min:1',
            'line_items.*.description' => 'required|string|max:500',
            'line_items.*.quantity' => 'required|integer|min:1',
            'line_items.*.unit_price' => 'required|numeric|min:0',
            'line_items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'terms_and_conditions' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Create invoice
            $invoice = Invoice::create([
                'property_id' => $request->property_id,
                'invoice_number' => Invoice::generateInvoiceNumber($request->property_id),
                'booking_id' => $request->booking_id,
                'guest_id' => $request->guest_id,
                'status' => 'draft',
                'type' => $request->type,
                'issue_date' => $request->issue_date,
                'due_date' => $request->due_date,
                'currency' => 'INR',
                'notes' => $request->notes,
                'terms_and_conditions' => $request->terms_and_conditions,
                'created_by_user_id' => auth()->id()
            ]);

            // Add line items
            foreach ($request->line_items as $itemData) {
                $invoice->addLineItem([
                    'item_type' => $itemData['item_type'] ?? 'other',
                    'description' => $itemData['description'],
                    'quantity' => $itemData['quantity'],
                    'unit_price_cents' => round($itemData['unit_price'] * 100),
                    'tax_rate' => $itemData['tax_rate'] ?? 0,
                    'discount_percentage' => $itemData['discount_percentage'] ?? 0
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Invoice created successfully',
                'data' => $invoice
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to create invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update invoice
     */
    public function invoiceUpdate(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        if (!$invoice->canEdit()) {
            return response()->json([
                'status' => false,
                'message' => 'Cannot edit invoice in current status'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'terms_and_conditions' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $invoice->update($request->only(['due_date', 'notes', 'terms_and_conditions']));
            $invoice->updated_by_user_id = auth()->id();
            $invoice->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Invoice updated successfully',
                'data' => $invoice
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel invoice
     */
    public function invoiceCancel(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $invoice = Invoice::findOrFail($id);

        DB::beginTransaction();
        try {
            $invoice->cancel($request->reason, auth()->id());
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Invoice cancelled successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Delete invoice
     */
    public function invoiceDestroy($id)
    {
        $invoice = Invoice::findOrFail($id);

        if (!$invoice->canDelete()) {
            return response()->json([
                'status' => false,
                'message' => 'Cannot delete invoice in current status or with payments'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $invoice->delete();
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Invoice deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate invoice from booking
     */
    public function generateFromBooking($bookingId)
    {
        $booking = Booking::with(['bookingRooms.roomType', 'bookingRooms.room', 'charges'])
            ->findOrFail($bookingId);

        // Check if invoice already exists
        $existingInvoice = Invoice::where('booking_id', $bookingId)->first();
        if ($existingInvoice) {
            return response()->json([
                'status' => false,
                'message' => 'Invoice already exists for this booking',
                'invoice_id' => $existingInvoice->id
            ], 422);
        }

        DB::beginTransaction();
        try {
            $invoice = Invoice::createFromBooking($booking);
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Invoice generated successfully',
                'data' => $invoice
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to generate invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send invoice via email
     */
    public function invoiceSendEmail($id)
    {
        $invoice = Invoice::findOrFail($id);

        try {
            $invoice->sendEmail();
            $invoice->markAsSent();

            return response()->json([
                'status' => true,
                'message' => 'Invoice sent successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to send invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate PDF invoice
     */
    public function invoicePDF($id)
    {
        $invoice = Invoice::with(['property', 'guest', 'booking', 'lineItems'])
            ->findOrFail($id);

        // TODO: Implement actual PDF generation
        // For now, return JSON
        return response()->json([
            'status' => true,
            'message' => 'PDF generation - To be implemented',
            'invoice' => $invoice
        ]);
    }

    // ============================================
    // PAYMENT MANAGEMENT
    // ============================================

    /**
     * Display payments listing
     */
    public function paymentIndex()
    {
        Breadcrumbs::add('Financial Management','/financials/invoices');
        Breadcrumbs::add('Payments');
        
        $properties = Property::all();
        return view('financials.payments.index', compact('properties'));
    }

    /**
     * Get payments data for DataTables
     */
    public function paymentAjax(Request $request)
    {
        $query = Payment::with(['property', 'guest', 'booking', 'invoice', 'receivedBy'])
            ->select(['payments.*'])
            ->orderBy('paid_at', 'desc');

        // Apply filters
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('payment_ref_display', function ($row) {
                return '<div class="fw-bold">' . $row->payment_reference . '</div>' .
                       '<small class="text-muted">' . ucfirst($row->type) . '</small>';
            })
            ->addColumn('guest_display', function ($row) {
                if ($row->guest) {
                    return '<div class="fw-bold">' . $row->guest->full_name . '</div>' .
                           '<small>' . $row->guest->phone . '</small>';
                }
                return '<span class="text-muted">-</span>';
            })
            ->addColumn('amount_display', function ($row) {
                $unallocated = $row->unallocated_amount;
                $badge = $unallocated > 0 ? '<span class="badge bg-warning ms-1">Unallocated</span>' : '';
                return '<div class="text-end">' .
                       '<div class="fw-bold">₹' . number_format($row->amount, 2) . $badge . '</div>' .
                       '<small class="text-muted">' . ucfirst(str_replace('_', ' ', $row->method)) . '</small>' .
                       '</div>';
            })
            ->addColumn('paid_at_display', function ($row) {
                return $row->paid_at ? $row->paid_at->format('d M Y H:i') : '-';
            })
            ->addColumn('status_badge', function ($row) {
                $colors = [
                    'pending' => 'warning',
                    'completed' => 'success',
                    'failed' => 'danger',
                    'cancelled' => 'secondary',
                    'refunded' => 'info'
                ];
                $color = $colors[$row->status] ?? 'secondary';
                return '<span class="badge bg-'.$color.'">'.ucfirst($row->status).'</span>';
            })
            ->addColumn('action', function ($row) {
                $actions = '
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item view-payment" href="#" data-id="'.$row->id.'">
                                <i class="bx bx-show"></i> View</a></li>';
                
                if ($row->unallocated_amount > 0) {
                    $actions .= '<li><a class="dropdown-item allocate-payment" href="#" data-id="'.$row->id.'">
                                <i class="bx bx-spreadsheet"></i> Allocate</a></li>';
                }
                
                $actions .= '<li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="'.route('financials.payments.receipt', $row->id).'" target="_blank">
                                <i class="bx bx-receipt"></i> Download Receipt</a></li>';
                
                if ($row->canRefund()) {
                    $actions .= '<li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item initiate-refund" href="#" data-id="'.$row->id.'">
                                <i class="bx bx-undo"></i> Initiate Refund</a></li>';
                }
                
                if ($row->canDelete()) {
                    $actions .= '<li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item delete-payment" href="#" data-id="'.$row->id.'">
                                <i class="bx bx-trash"></i> Delete</a></li>';
                }
                
                $actions .= '</ul></div>';
                
                return $actions;
            })
            ->rawColumns(['payment_ref_display', 'guest_display', 'amount_display', 'status_badge', 'action'])
            ->make(true);
    }

    /**
     * Show payment details
     */
    public function paymentShow($id)
    {
        $payment = Payment::with([
            'property',
            'guest',
            'booking',
            'invoice',
            'allocations.invoice',
            'refund',
            'receivedBy'
        ])->findOrFail($id);

        return response()->json($payment);
    }

    /**
     * Store new payment
     */
    public function paymentStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'property_id' => 'required|exists:properties,id',
            'guest_id' => 'nullable|exists:guests,id',
            'booking_id' => 'nullable|exists:bookings,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,card,upi,net_banking,cheque,wallet,bank_transfer,other',
            'type' => 'nullable|in:payment,refund,advance,security_deposit',
            'transaction_id' => 'nullable|string|max:100',
            'cheque_number' => 'nullable|string|max:50',
            'cheque_date' => 'nullable|date',
            'bank_name' => 'nullable|string|max:100',
            'remarks' => 'nullable|string',
            'paid_at' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $payment = Payment::create([
                'property_id' => $request->property_id,
                'payment_reference' => Payment::generatePaymentReference($request->property_id),
                'guest_id' => $request->guest_id,
                'booking_id' => $request->booking_id,
                'invoice_id' => $request->invoice_id,
                'amount_cents' => round($request->amount * 100),
                'currency' => 'INR',
                'type' => $request->type ?? 'payment',
                'method' => $request->method,
                'status' => 'completed',
                'transaction_id' => $request->transaction_id,
                'cheque_number' => $request->cheque_number,
                'cheque_date' => $request->cheque_date,
                'bank_name' => $request->bank_name,
                'paid_at' => $request->paid_at ?? now(),
                'remarks' => $request->remarks,
                'received_by_user_id' => auth()->id()
            ]);

            // Auto-allocate to invoice if provided
            if ($request->invoice_id) {
                $invoice = Invoice::find($request->invoice_id);
                $payment->allocateToInvoice($invoice);
            } 
            // Or auto-allocate to booking/guest invoices
            elseif ($request->booking_id || $request->guest_id) {
                $payment->autoAllocate();
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Payment recorded successfully',
                'data' => $payment
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to record payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Allocate payment to invoice
     */
    public function allocatePayment(Request $request, $id)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'nullable|numeric|min:0.01'
        ]);

        $payment = Payment::findOrFail($id);
        $invoice = Invoice::findOrFail($request->invoice_id);

        DB::beginTransaction();
        try {
            $allocation = $payment->allocateToInvoice($invoice, $request->amount);
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Payment allocated successfully',
                'data' => $allocation
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Generate payment receipt
     */
    public function paymentReceipt($id)
    {
        $payment = Payment::with(['property', 'guest', 'booking', 'allocations.invoice'])
            ->findOrFail($id);

        // TODO: Implement actual PDF generation
        return response()->json([
            'status' => true,
            'message' => 'Receipt generation - To be implemented',
            'payment' => $payment
        ]);
    }

    // ============================================
    // REFUND MANAGEMENT
    // ============================================

    /**
     * Initiate refund
     */
    public function initiateRefund(Request $request, $paymentId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|in:cancellation,overpayment,goodwill,dispute,other',
            'reason_description' => 'required|string|max:500',
            'method' => 'nullable|in:cash,card,upi,bank_transfer,original_method,other'
        ]);

        $payment = Payment::findOrFail($paymentId);

        DB::beginTransaction();
        try {
            $refund = $payment->initiateRefund(
                $request->amount,
                $request->reason,
                $request->only(['reason_description', 'method', 'internal_notes'])
            );

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Refund initiated successfully',
                'data' => $refund
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Approve refund
     */
    public function approveRefund($id)
    {
        $refund = Refund::findOrFail($id);

        DB::beginTransaction();
        try {
            $refund->approve(auth()->id());
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Refund approved successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Process refund
     */
    public function processRefund($id)
    {
        $refund = Refund::findOrFail($id);

        DB::beginTransaction();
        try {
            $refund->process(auth()->id());
            // Here you would integrate with payment gateway
            // For now, we'll mark it as completed directly
            $refund->complete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Refund processed successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Cancel refund
     */
    public function cancelRefund(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $refund = Refund::findOrFail($id);

        DB::beginTransaction();
        try {
            $refund->cancel($request->reason, auth()->id());
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Refund cancelled successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    // ============================================
    // REPORTS & ANALYTICS
    // ============================================

    /**
     * Get financial summary/stats
     */
    public function getFinancialStats(Request $request)
    {
        $propertyId = $request->get('property_id');
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $invoiceQuery = Invoice::query();
        $paymentQuery = Payment::query();

        if ($propertyId) {
            $invoiceQuery->where('property_id', $propertyId);
            $paymentQuery->where('property_id', $propertyId);
        }

        $invoiceQuery->whereBetween('issue_date', [$startDate, $endDate]);
        $paymentQuery->whereBetween('paid_at', [$startDate, $endDate]);

        $stats = [
            'total_invoiced' => $invoiceQuery->sum('total_cents') / 100,
            'total_paid' => $paymentQuery->where('status', 'completed')->sum('amount_cents') / 100,
            'outstanding' => $invoiceQuery->sum('balance_cents') / 100,
            'overdue' => Invoice::overdue()->sum('balance_cents') / 100,
            'invoice_count' => $invoiceQuery->count(),
            'payment_count' => $paymentQuery->where('status', 'completed')->count(),
            'average_invoice' => $invoiceQuery->avg('total_cents') / 100,
            'pending_refunds' => Refund::where('status', 'pending')->sum('amount_cents') / 100
        ];

        return response()->json([
            'status' => true,
            'stats' => $stats
        ]);
    }
}