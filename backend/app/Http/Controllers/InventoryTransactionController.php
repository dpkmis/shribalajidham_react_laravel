<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\Property;
use App\Models\InventoryCategory;
use App\Services\Breadcrumbs;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class InventoryTransactionController extends Controller
{
    // Show listing view
    public function index()
    {
        Breadcrumbs::add('Inventory Management', route('inventory.index'));
        Breadcrumbs::add('Transactions');
        
        $properties = Property::all();
        $categories = InventoryCategory::active()->ordered()->get();
        $transactionTypes = InventoryTransaction::getTransactionTypes();

        return view('inventory.transactions.index', compact('properties', 'categories', 'transactionTypes'));
    }

    public function ajaxTransactions(Request $request)
    {
        $query = InventoryTransaction::with(['item.category', 'property', 'user', 'item'])
            ->select(['inventory_transactions.*'])
            ->orderBy('inventory_transactions.created_at', 'desc');

        // Apply property filter if set
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        // Apply item filter if set
        if ($request->filled('item_id')) {
            $query->where('inventory_item_id', $request->item_id);
        }

        // Apply category filter if set
        if ($request->filled('category_id')) {
            $query->whereHas('item', function($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        // Apply column filters
        if ($request->has('columns')) {
            foreach ($request->get('columns') as $col) {
                $colName = $col['data'];
                $searchValue = $col['search']['value'];

                if (!empty($searchValue)) {
                    if ($colName === 'item.name') {
                        $query->whereHas('item', function ($q) use ($searchValue) {
                            $q->where('name', 'like', "%{$searchValue}%");
                        });
                    } elseif ($colName === 'user.name') {
                        $query->whereHas('user', function ($q) use ($searchValue) {
                            $q->where('name', 'like', "%{$searchValue}%");
                        });
                    } elseif ($colName === 'transaction_date') {
                        $dates = explode(' - ', $searchValue);
                        if (count($dates) === 2) {
                            $start = date('Y-m-d 00:00:00', strtotime($dates[0]));
                            $end = date('Y-m-d 23:59:59', strtotime($dates[1]));
                            $query->whereBetween('inventory_transactions.transaction_date', [$start, $end]);
                        }
                    } elseif ($colName === 'transaction_type') {
                        $query->where('inventory_transactions.transaction_type', $searchValue);
                    }
                }
            }
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('item_display', function ($row) {
                $html = '<div>';
                $html .= '<strong>' . $row->item->name . '</strong><br>';
                $html .= '<small class="text-muted">' . $row->item->item_code . '</small>';
                if ($row->item->category) {
                    $html .= ' <span class="badge bg-secondary">' . $row->item->category->name . '</span>';
                }
                $html .= '</div>';
                return $html;
            })
            ->addColumn('transaction_type_badge', function ($row) {
                $colors = [
                    'stock_in' => 'success',
                    'stock_out' => 'danger',
                    'adjustment' => 'warning',
                    'transfer' => 'info',
                    'damage' => 'dark',
                    'expired' => 'secondary',
                    'return' => 'primary'
                ];
                $icons = [
                    'stock_in' => 'bx-plus',
                    'stock_out' => 'bx-minus',
                    'adjustment' => 'bx-sync',
                    'transfer' => 'bx-transfer',
                    'damage' => 'bx-x',
                    'expired' => 'bx-time',
                    'return' => 'bx-undo'
                ];
                $color = $colors[$row->transaction_type] ?? 'secondary';
                $icon = $icons[$row->transaction_type] ?? 'bx-circle';
                $label = str_replace('_', ' ', ucwords($row->transaction_type, '_'));
                
                return '<span class="badge bg-'.$color.'"><i class="bx '.$icon.'"></i> '.$label.'</span>';
            })
            ->addColumn('quantity_display', function ($row) {
                $color = $row->quantity >= 0 ? 'success' : 'danger';
                $sign = $row->quantity >= 0 ? '+' : '';
                $value = $sign . number_format($row->quantity, 2);
                
                return '<span class="text-'.$color.' fw-bold">'.$value.' '.$row->item->unit.'</span>';
            })
            ->addColumn('balance_display', function ($row) {
                return '<span class="fw-bold">'.number_format($row->balance_after, 2).' '.$row->item->unit.'</span>';
            })
            ->addColumn('value_display', function ($row) {
                if ($row->unit_price_cents) {
                    $totalValue = ($row->unit_price_cents / 100) * abs($row->quantity);
                    return '₹' . number_format($totalValue, 2);
                }
                return '-';
            })
            ->addColumn('user_display', function ($row) {
                return $row->user ? $row->user->name : '<span class="text-muted">System</span>';
            })
            ->addColumn('date_display', function ($row) {
                return '<div>'.
                    $row->transaction_date->format('d M Y').'<br>'.
                    '<small class="text-muted">'.$row->created_at->format('h:i A').'</small>'.
                    '</div>';
            })
            ->addColumn('reference_display', function ($row) {
                if ($row->reference_type && $row->reference_id) {
                    return '<span class="badge bg-info">'.ucfirst($row->reference_type).' #'.$row->reference_id.'</span>';
                }
                return '-';
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="dropdown ms-auto">
                        <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-horizontal-rounded text-option"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item view-transaction" href="#" data-id="'.$row->id.'"><i class="bx bx-show"></i> View Details</a></li>
                            <li><a class="dropdown-item view-item" href="#" data-id="'.$row->inventory_item_id.'"><i class="bx bx-package"></i> View Item</a></li>
                        </ul>
                    </div>
                ';
            })
            ->rawColumns(['item_display', 'transaction_type_badge', 'quantity_display', 'balance_display', 'value_display', 'user_display', 'date_display', 'reference_display', 'action'])
            ->make(true);
    }

    // Show single transaction
    public function show($id)
    {
        $transaction = InventoryTransaction::with([
            'item.category',
            'property',
            'user',
            'fromLocation',
            'toLocation'
        ])->findOrFail($id);
        
        $transaction->quantity_display = number_format($transaction->quantity, 2);
        $transaction->balance_display = number_format($transaction->balance_after, 2);
        $transaction->unit_price_display = $transaction->unit_price;
        $transaction->date_display = $transaction->transaction_date->format('d M Y');
        $transaction->time_display = $transaction->created_at->format('h:i A');
        
        return response()->json($transaction);
    }

    // Get transactions by item
    public function getByItem($itemId)
    {
        $item = InventoryItem::findOrFail($itemId);
        
        $transactions = InventoryTransaction::with(['user', 'property'])
            ->where('inventory_item_id', $itemId)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'status' => true,
            'item' => $item,
            'transactions' => $transactions
        ]);
    }

    // Get transactions report
    public function getReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'property_id' => 'nullable|exists:properties,id',
            'category_id' => 'nullable|exists:inventory_categories,id',
            'transaction_type' => 'nullable|in:stock_in,stock_out,adjustment,transfer,damage,expired,return',
        ]);

        $query = InventoryTransaction::with(['item.category', 'property', 'user'])
            ->whereBetween('transaction_date', [$request->start_date, $request->end_date]);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('category_id')) {
            $query->whereHas('item', function($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->get();

        // Calculate summary
        $summary = [
            'total_transactions' => $transactions->count(),
            'stock_in_count' => $transactions->where('transaction_type', 'stock_in')->count(),
            'stock_out_count' => $transactions->where('transaction_type', 'stock_out')->count(),
            'adjustment_count' => $transactions->where('transaction_type', 'adjustment')->count(),
            'total_value_in' => $transactions->where('transaction_type', 'stock_in')
                ->sum(function($t) {
                    return ($t->unit_price_cents / 100) * $t->quantity;
                }),
            'total_value_out' => $transactions->where('transaction_type', 'stock_out')
                ->sum(function($t) {
                    return ($t->unit_price_cents / 100) * abs($t->quantity);
                }),
        ];

        return response()->json([
            'status' => true,
            'data' => $transactions,
            'summary' => $summary
        ]);
    }

    // Get daily summary
    public function getDailySummary(Request $request)
    {
        $date = $request->input('date', today());
        
        $query = InventoryTransaction::with(['item'])
            ->whereDate('transaction_date', $date);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        $transactions = $query->get();

        $summary = [
            'date' => Carbon::parse($date)->format('d M Y'),
            'total_transactions' => $transactions->count(),
            'by_type' => [
                'stock_in' => $transactions->where('transaction_type', 'stock_in')->count(),
                'stock_out' => $transactions->where('transaction_type', 'stock_out')->count(),
                'adjustment' => $transactions->where('transaction_type', 'adjustment')->count(),
                'damage' => $transactions->where('transaction_type', 'damage')->count(),
                'expired' => $transactions->where('transaction_type', 'expired')->count(),
            ],
            'most_used_items' => $transactions
                ->where('transaction_type', 'stock_out')
                ->groupBy('inventory_item_id')
                ->map(function($group) {
                    return [
                        'item_name' => $group->first()->item->name,
                        'quantity' => abs($group->sum('quantity')),
                        'unit' => $group->first()->item->unit,
                    ];
                })
                ->sortByDesc('quantity')
                ->take(5)
                ->values(),
            'total_value_in' => $transactions->where('transaction_type', 'stock_in')
                ->sum(function($t) {
                    return ($t->unit_price_cents / 100) * $t->quantity;
                }),
            'total_value_out' => $transactions->where('transaction_type', 'stock_out')
                ->sum(function($t) {
                    return ($t->unit_price_cents / 100) * abs($t->quantity);
                }),
        ];

        return response()->json([
            'status' => true,
            'data' => $summary
        ]);
    }

    // Get stock movement report
    public function getStockMovement(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $item = InventoryItem::findOrFail($request->item_id);
        
        $transactions = InventoryTransaction::where('inventory_item_id', $request->item_id)
            ->whereBetween('transaction_date', [$request->start_date, $request->end_date])
            ->orderBy('transaction_date')
            ->orderBy('created_at')
            ->get();

        $movement = [
            'item' => [
                'id' => $item->id,
                'name' => $item->name,
                'code' => $item->item_code,
                'current_stock' => $item->current_stock,
                'unit' => $item->unit,
            ],
            'period' => [
                'start' => Carbon::parse($request->start_date)->format('d M Y'),
                'end' => Carbon::parse($request->end_date)->format('d M Y'),
            ],
            'opening_balance' => $transactions->first()->balance_after - $transactions->first()->quantity,
            'closing_balance' => $transactions->last()->balance_after,
            'total_in' => $transactions->where('quantity', '>', 0)->sum('quantity'),
            'total_out' => abs($transactions->where('quantity', '<', 0)->sum('quantity')),
            'transactions' => $transactions->map(function($t) {
                return [
                    'date' => $t->transaction_date->format('d M Y'),
                    'type' => $t->transaction_type,
                    'quantity' => $t->quantity,
                    'balance' => $t->balance_after,
                    'remarks' => $t->remarks,
                    'user' => $t->user ? $t->user->name : 'System',
                ];
            }),
        ];

        return response()->json([
            'status' => true,
            'data' => $movement
        ]);
    }

    // Get consumption analysis
    public function getConsumptionAnalysis(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'property_id' => 'nullable|exists:properties,id',
            'category_id' => 'nullable|exists:inventory_categories,id',
        ]);

        $query = InventoryTransaction::with(['item.category'])
            ->where('transaction_type', 'stock_out')
            ->whereBetween('transaction_date', [$request->start_date, $request->end_date]);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('category_id')) {
            $query->whereHas('item', function($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        $transactions = $query->get();

        // Group by item
        $itemConsumption = $transactions->groupBy('inventory_item_id')->map(function($group) {
            $item = $group->first()->item;
            $totalQuantity = abs($group->sum('quantity'));
            $totalValue = $group->sum(function($t) {
                return ($t->unit_price_cents / 100) * abs($t->quantity);
            });

            return [
                'item_id' => $item->id,
                'item_name' => $item->name,
                'item_code' => $item->item_code,
                'category' => $item->category ? $item->category->name : 'Uncategorized',
                'total_consumed' => $totalQuantity,
                'unit' => $item->unit,
                'total_value' => $totalValue,
                'transaction_count' => $group->count(),
                'average_per_transaction' => $totalQuantity / $group->count(),
            ];
        })->sortByDesc('total_value')->values();

        // Group by category
        $categoryConsumption = $transactions->groupBy('item.category_id')->map(function($group) {
            $category = $group->first()->item->category;
            $totalValue = $group->sum(function($t) {
                return ($t->unit_price_cents / 100) * abs($t->quantity);
            });

            return [
                'category_id' => $category ? $category->id : null,
                'category_name' => $category ? $category->name : 'Uncategorized',
                'total_value' => $totalValue,
                'transaction_count' => $group->count(),
                'unique_items' => $group->unique('inventory_item_id')->count(),
            ];
        })->sortByDesc('total_value')->values();

        // Daily consumption trend
        $dailyTrend = $transactions->groupBy(function($t) {
            return $t->transaction_date->format('Y-m-d');
        })->map(function($group, $date) {
            return [
                'date' => Carbon::parse($date)->format('d M Y'),
                'total_value' => $group->sum(function($t) {
                    return ($t->unit_price_cents / 100) * abs($t->quantity);
                }),
                'transaction_count' => $group->count(),
            ];
        })->sortBy('date')->values();

        $analysis = [
            'period' => [
                'start' => Carbon::parse($request->start_date)->format('d M Y'),
                'end' => Carbon::parse($request->end_date)->format('d M Y'),
                'days' => Carbon::parse($request->start_date)->diffInDays($request->end_date) + 1,
            ],
            'summary' => [
                'total_transactions' => $transactions->count(),
                'total_value' => $transactions->sum(function($t) {
                    return ($t->unit_price_cents / 100) * abs($t->quantity);
                }),
                'unique_items' => $transactions->unique('inventory_item_id')->count(),
                'average_daily_value' => $transactions->sum(function($t) {
                    return ($t->unit_price_cents / 100) * abs($t->quantity);
                }) / (Carbon::parse($request->start_date)->diffInDays($request->end_date) + 1),
            ],
            'top_items' => $itemConsumption->take(10),
            'by_category' => $categoryConsumption,
            'daily_trend' => $dailyTrend,
        ];

        return response()->json([
            'status' => true,
            'data' => $analysis
        ]);
    }

    // Get wastage report
    public function getWastageReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'property_id' => 'nullable|exists:properties,id',
        ]);

        $query = InventoryTransaction::with(['item.category', 'user'])
            ->whereIn('transaction_type', ['damage', 'expired'])
            ->whereBetween('transaction_date', [$request->start_date, $request->end_date]);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        $transactions = $query->get();

        $wastage = [
            'period' => [
                'start' => Carbon::parse($request->start_date)->format('d M Y'),
                'end' => Carbon::parse($request->end_date)->format('d M Y'),
            ],
            'summary' => [
                'total_wastage_count' => $transactions->count(),
                'damage_count' => $transactions->where('transaction_type', 'damage')->count(),
                'expired_count' => $transactions->where('transaction_type', 'expired')->count(),
                'total_value_lost' => $transactions->sum(function($t) {
                    return ($t->unit_price_cents / 100) * abs($t->quantity);
                }),
            ],
            'by_type' => [
                'damage' => [
                    'count' => $transactions->where('transaction_type', 'damage')->count(),
                    'value' => $transactions->where('transaction_type', 'damage')
                        ->sum(function($t) {
                            return ($t->unit_price_cents / 100) * abs($t->quantity);
                        }),
                ],
                'expired' => [
                    'count' => $transactions->where('transaction_type', 'expired')->count(),
                    'value' => $transactions->where('transaction_type', 'expired')
                        ->sum(function($t) {
                            return ($t->unit_price_cents / 100) * abs($t->quantity);
                        }),
                ],
            ],
            'top_items' => $transactions->groupBy('inventory_item_id')->map(function($group) {
                $item = $group->first()->item;
                return [
                    'item_name' => $item->name,
                    'category' => $item->category ? $item->category->name : 'Uncategorized',
                    'quantity' => abs($group->sum('quantity')),
                    'unit' => $item->unit,
                    'value_lost' => $group->sum(function($t) {
                        return ($t->unit_price_cents / 100) * abs($t->quantity);
                    }),
                ];
            })->sortByDesc('value_lost')->take(10)->values(),
            'transactions' => $transactions->map(function($t) {
                return [
                    'date' => $t->transaction_date->format('d M Y'),
                    'item_name' => $t->item->name,
                    'type' => $t->transaction_type,
                    'quantity' => abs($t->quantity),
                    'unit' => $t->item->unit,
                    'value' => ($t->unit_price_cents / 100) * abs($t->quantity),
                    'remarks' => $t->remarks,
                    'user' => $t->user ? $t->user->name : 'System',
                ];
            }),
        ];

        return response()->json([
            'status' => true,
            'data' => $wastage
        ]);
    }

    // Export transactions
    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'property_id' => 'nullable|exists:properties,id',
            'format' => 'required|in:csv,excel,pdf',
        ]);

        // Get transactions
        $query = InventoryTransaction::with(['item.category', 'property', 'user'])
            ->whereBetween('transaction_date', [$request->start_date, $request->end_date]);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->get();

        // TODO: Implement actual export logic based on format
        // For now, return JSON
        return response()->json([
            'status' => true,
            'message' => 'Export functionality would generate ' . $request->format . ' file',
            'count' => $transactions->count()
        ]);
    }
}