<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryCategory;
use App\Models\InventoryTransaction;
use App\Models\Property;
use App\Services\Breadcrumbs;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    // Show listing view
    public function index()
    {
        Breadcrumbs::add('Inventory Management', route('inventory.index'));
        Breadcrumbs::add('Inventory Items');
        
        $properties = Property::all();
        $categories = InventoryCategory::active()->ordered()->get();
        $statusOptions = InventoryItem::getStatusOptions();
        $units = ['pcs', 'kg', 'ltr', 'box', 'bottle', 'packet', 'roll', 'set', 'dozen'];

        return view('inventory.index', compact('properties', 'categories', 'statusOptions', 'units'));
    }

    public function ajaxInventory(Request $request)
    {
        $query = InventoryItem::with(['property', 'category'])
            ->select(['inventory_items.*']);

        // Apply property filter if set
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        // Apply column filters
        if ($request->has('columns')) {
            foreach ($request->get('columns') as $col) {
                $colName = $col['data'];
                $searchValue = $col['search']['value'];

                if (!empty($searchValue)) {
                    if ($colName === 'property.name') {
                        $query->whereHas('property', function ($q) use ($searchValue) {
                            $q->where('name', 'like', "%{$searchValue}%");
                        });
                    } elseif ($colName === 'category.name') {
                        $query->whereHas('category', function ($q) use ($searchValue) {
                            $q->where('name', 'like', "%{$searchValue}%");
                        });
                    } elseif ($colName === 'created_at') {
                        $dates = explode(' - ', $searchValue);
                        if (count($dates) === 2) {
                            $start = date('Y-m-d 00:00:00', strtotime($dates[0]));
                            $end = date('Y-m-d 23:59:59', strtotime($dates[1]));
                            $query->whereBetween('inventory_items.created_at', [$start, $end]);
                        }
                    } elseif (in_array($colName, ['name', 'item_code', 'status'])) {
                        $query->where("inventory_items.{$colName}", 'like', "%{$searchValue}%");
                    }
                }
            }
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('stock_display', function ($row) {
                $statusColors = [
                    'out_of_stock' => 'danger',
                    'low_stock' => 'warning',
                    'reorder' => 'info',
                    'overstock' => 'secondary',
                    'normal' => 'success'
                ];
                $color = $statusColors[$row->stock_status] ?? 'success';
                $stockText = number_format($row->current_stock, 2) . ' ' . $row->unit;
                
                return '<div class="d-flex flex-column">
                    <span class="fw-bold">' . $stockText . '</span>
                    <small class="text-muted">Min: ' . $row->min_stock . ' | Max: ' . $row->max_stock . '</small>
                </div>';
            })
            ->addColumn('stock_status_badge', function ($row) {
                $badges = [
                    'out_of_stock' => '<span class="badge bg-danger"><i class="bx bx-error"></i> Out of Stock</span>',
                    'low_stock' => '<span class="badge bg-warning"><i class="bx bx-down-arrow-alt"></i> Low Stock</span>',
                    'reorder' => '<span class="badge bg-info"><i class="bx bx-refresh"></i> Reorder</span>',
                    'overstock' => '<span class="badge bg-secondary"><i class="bx bx-up-arrow-alt"></i> Overstock</span>',
                    'normal' => '<span class="badge bg-success"><i class="bx bx-check"></i> Normal</span>'
                ];
                return $badges[$row->stock_status] ?? $badges['normal'];
            })
            ->addColumn('value_display', function ($row) {
                return '₹' . number_format($row->stock_value, 2);
            })
            ->addColumn('status_badge', function ($row) {
                $colors = [
                    'active' => 'success',
                    'inactive' => 'secondary',
                    'discontinued' => 'danger'
                ];
                $color = $colors[$row->status] ?? 'secondary';
                return '<span class="badge bg-'.$color.'">'.ucfirst($row->status).'</span>';
            })
            ->addColumn('expiry_display', function ($row) {
                if (!$row->is_perishable || !$row->expiry_date) {
                    return '<span class="text-muted">N/A</span>';
                }
                
                $daysUntilExpiry = now()->diffInDays($row->expiry_date, false);
                
                if ($daysUntilExpiry < 0) {
                    return '<span class="badge bg-danger"><i class="bx bx-x"></i> Expired</span>';
                } elseif ($daysUntilExpiry <= 7) {
                    return '<span class="badge bg-danger">' . $row->expiry_date->format('d M Y') . '</span>';
                } elseif ($daysUntilExpiry <= 30) {
                    return '<span class="badge bg-warning">' . $row->expiry_date->format('d M Y') . '</span>';
                }
                
                return '<span class="text-muted">' . $row->expiry_date->format('d M Y') . '</span>';
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="dropdown ms-auto">
                        <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-horizontal-rounded text-option"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item edit-item" href="#" data-id="'.$row->id.'"><i class="bx bx-edit"></i> Edit</a></li>
                            <li><a class="dropdown-item stock-in" href="#" data-id="'.$row->id.'"><i class="bx bx-plus"></i> Stock In</a></li>
                            <li><a class="dropdown-item stock-out" href="#" data-id="'.$row->id.'"><i class="bx bx-minus"></i> Stock Out</a></li>
                            <li><a class="dropdown-item adjust-stock" href="#" data-id="'.$row->id.'"><i class="bx bx-sync"></i> Adjust Stock</a></li>
                            <li><a class="dropdown-item view-history" href="#" data-id="'.$row->id.'"><i class="bx bx-history"></i> View History</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a href="javascript:void(0);" class="dropdown-item text-danger delete-item" data-id="'.$row->id.'"><i class="bx bx-trash"></i> Delete</a></li>
                        </ul>
                    </div>
                ';
            })
            ->rawColumns(['stock_display', 'stock_status_badge', 'status_badge', 'expiry_display', 'action'])
            ->make(true);
    }

    // Store new inventory item
    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'category_id' => 'required|exists:inventory_categories,id',
            'item_code' => 'required|string|max:50|unique:inventory_items,item_code',
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'current_stock' => 'required|numeric|min:0',
            'min_stock' => 'required|numeric|min:0',
            'max_stock' => 'required|numeric|min:0|gte:min_stock',
            'reorder_point' => 'nullable|numeric|min:0',
            'unit_price' => 'nullable|numeric|min:0',
            'storage_location' => 'nullable|string|max:200',
            'bin_location' => 'nullable|string|max:50',
            'supplier_name' => 'nullable|string|max:200',
            'supplier_code' => 'nullable|string|max:50',
            'expiry_date' => 'nullable|date|after:today',
            'batch_number' => 'nullable|string|max:50',
            'is_perishable' => 'boolean',
            'requires_approval' => 'boolean',
            'status' => 'required|in:active,inactive,discontinued',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $item = InventoryItem::create([
                'property_id' => $request->property_id,
                'category_id' => $request->category_id,
                'item_code' => $request->item_code,
                'name' => $request->name,
                'description' => $request->description,
                'unit' => $request->unit,
                'current_stock' => $request->current_stock,
                'min_stock' => $request->min_stock,
                'max_stock' => $request->max_stock,
                'reorder_point' => $request->reorder_point ?? $request->min_stock,
                'unit_price_cents' => $request->unit_price ? $request->unit_price * 100 : null,
                'storage_location' => $request->storage_location,
                'bin_location' => $request->bin_location,
                'supplier_name' => $request->supplier_name,
                'supplier_code' => $request->supplier_code,
                'expiry_date' => $request->expiry_date,
                'batch_number' => $request->batch_number,
                'is_perishable' => $request->is_perishable ?? false,
                'requires_approval' => $request->requires_approval ?? false,
                'status' => $request->status,
                'is_active' => $request->is_active ?? true,
                'notes' => $request->notes,
            ]);

            // Create initial stock transaction
            if ($request->current_stock > 0) {
                InventoryTransaction::create([
                    'inventory_item_id' => $item->id,
                    'property_id' => $item->property_id,
                    'user_id' => Auth::id(),
                    'transaction_type' => 'stock_in',
                    'quantity' => $request->current_stock,
                    'balance_after' => $request->current_stock,
                    'unit_price_cents' => $request->unit_price ? $request->unit_price * 100 : null,
                    'remarks' => 'Initial stock',
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Inventory item created successfully',
                'data' => $item
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to create inventory item: ' . $e->getMessage()
            ], 500);
        }
    }

    // Show single item
    public function show($id)
    {
        $item = InventoryItem::with(['property', 'category'])->findOrFail($id);
        $item->unit_price_display = $item->unit_price;
        $item->expiry_date_display = $item->expiry_date ? $item->expiry_date->format('Y-m-d') : null;
        
        return response()->json($item);
    }

    // Update inventory item
    public function update(Request $request, $id)
    {
        $item = InventoryItem::findOrFail($id);

        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'category_id' => 'required|exists:inventory_categories,id',
            'item_code' => 'required|string|max:50|unique:inventory_items,item_code,'.$id,
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'min_stock' => 'required|numeric|min:0',
            'max_stock' => 'required|numeric|min:0|gte:min_stock',
            'reorder_point' => 'nullable|numeric|min:0',
            'unit_price' => 'nullable|numeric|min:0',
            'storage_location' => 'nullable|string|max:200',
            'bin_location' => 'nullable|string|max:50',
            'supplier_name' => 'nullable|string|max:200',
            'supplier_code' => 'nullable|string|max:50',
            'expiry_date' => 'nullable|date',
            'batch_number' => 'nullable|string|max:50',
            'is_perishable' => 'boolean',
            'requires_approval' => 'boolean',
            'status' => 'required|in:active,inactive,discontinued',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $item->update([
                'property_id' => $request->property_id,
                'category_id' => $request->category_id,
                'item_code' => $request->item_code,
                'name' => $request->name,
                'description' => $request->description,
                'unit' => $request->unit,
                'min_stock' => $request->min_stock,
                'max_stock' => $request->max_stock,
                'reorder_point' => $request->reorder_point ?? $request->min_stock,
                'unit_price_cents' => $request->unit_price ? $request->unit_price * 100 : null,
                'storage_location' => $request->storage_location,
                'bin_location' => $request->bin_location,
                'supplier_name' => $request->supplier_name,
                'supplier_code' => $request->supplier_code,
                'expiry_date' => $request->expiry_date,
                'batch_number' => $request->batch_number,
                'is_perishable' => $request->is_perishable ?? false,
                'requires_approval' => $request->requires_approval ?? false,
                'status' => $request->status,
                'is_active' => $request->is_active ?? true,
                'notes' => $request->notes,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Inventory item updated successfully',
                'data' => $item
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update inventory item: ' . $e->getMessage()
            ], 500);
        }
    }

    // Delete item
    public function destroy($id)
    {
        $item = InventoryItem::find($id);

        if (!$item) {
            return response()->json([
                'status' => false,
                'message' => 'Inventory item not found'
            ], 404);
        }

        DB::beginTransaction();
        try {
            $item->delete();
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Inventory item deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete inventory item: ' . $e->getMessage()
            ], 500);
        }
    }

    // Stock In
    public function stockIn(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'quantity' => 'required|numeric|min:0.01',
            'unit_price' => 'nullable|numeric|min:0',
            'batch_number' => 'nullable|string|max:50',
            'expiry_date' => 'nullable|date|after:today',
            'remarks' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $item = InventoryItem::findOrFail($request->item_id);
            
            // Update batch and expiry if provided
            if ($request->filled('batch_number')) {
                $item->batch_number = $request->batch_number;
            }
            if ($request->filled('expiry_date')) {
                $item->expiry_date = $request->expiry_date;
            }
            $item->save();

            // Create transaction
            $item->addStock(
                $request->quantity,
                Auth::id(),
                $request->remarks,
                $request->unit_price
            );

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Stock added successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to add stock: ' . $e->getMessage()
            ], 500);
        }
    }

    // Stock Out
    public function stockOut(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'quantity' => 'required|numeric|min:0.01',
            'reference_type' => 'nullable|string|max:50',
            'reference_id' => 'nullable|integer',
            'remarks' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $item = InventoryItem::findOrFail($request->item_id);
            
            $item->removeStock(
                $request->quantity,
                Auth::id(),
                $request->remarks,
                $request->reference_type,
                $request->reference_id
            );

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Stock removed successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Adjust Stock
    public function adjustStock(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'new_quantity' => 'required|numeric|min:0',
            'remarks' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $item = InventoryItem::findOrFail($request->item_id);
            
            $item->adjustStock(
                $request->new_quantity,
                Auth::id(),
                $request->remarks
            );

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Stock adjusted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to adjust stock: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get transaction history
    public function getHistory($id)
    {
        $item = InventoryItem::findOrFail($id);
        $transactions = $item->transactions()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $transactions
        ]);
    }

    // Get low stock items
    public function getLowStockItems(Request $request)
    {
        $query = InventoryItem::with(['property', 'category'])
            ->lowStock()
            ->active();

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        $items = $query->ordered()->get();

        return response()->json([
            'status' => true,
            'count' => $items->count(),
            'data' => $items
        ]);
    }

    // Get expiring items
    public function getExpiringItems(Request $request)
    {
        $days = $request->input('days', 30);
        
        $query = InventoryItem::with(['property', 'category'])
            ->expiringSoon($days)
            ->active();

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        $items = $query->ordered()->get();

        return response()->json([
            'status' => true,
            'count' => $items->count(),
            'data' => $items
        ]);
    }
}