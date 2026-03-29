<?php

namespace App\Http\Controllers;

use App\Models\MasterFaqCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use App\Services\Breadcrumbs;



class MasterFaqCategoriesController extends Controller
{
    public function index()
    {
        Breadcrumbs::add('Dashboard', route('dashboard'));
        Breadcrumbs::add('Master Data');
        Breadcrumbs::add('Faq Categories');
        $languages = \DB::table('ugc_language')->select('id', 'identifier')->get();
        return view('master_faq_categories.index', compact('languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'display_title'       => 'required|string|max:255',
            'content_language_id' => 'required|integer|exists:ugc_language,id',
            'title'               => 'required|array', // array of {content, language}
            'status'              => 'required|boolean',
        ]);

        try {
            $titlesArray = $request->title;
            if (empty($titlesArray)) {
                return response()->json([
                    'status'  => 400,
                    'message' => 'Please provide at least one language content!',
                ]);
            }

            // Convert to key-value JSON: language => content
            $titleJson = [];
            foreach ($titlesArray as $t) {
                if (!empty($t['language']) && !empty($t['content'])) {
                    $titleJson[$t['language']] = $t['content'];
                }
            }

            $category = MasterFaqCategory::create([
                'display_name'   => $request->display_title,
                'category_name'  => json_encode($titleJson, JSON_UNESCAPED_UNICODE),
                'lang_id'        => $request->content_language_id,
                'status'         => $request->status,
                'position'       => MasterFaqCategory::max('position') + 1,
                'modified_at'    => time(),
                'created_at'     => time(),
                'created_by'     => auth()->id() ?? 0,
            ]);

            return response()->json([
                'status'  => 200,
                'message' => 'Category added successfully!',
                'data'    => $category
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ]);
        }
    }


    public function savePositions(Request $request)
    {
        $positions = $request->positions; // array of {id, position}

        foreach ($positions as $pos) {
            MasterFaqCategory::where('id', $pos['id'])->update(['position' => $pos['position']]);
        }

        return response()->json(['message' => 'Positions updated successfully!']);
    }




    /**
     * Display the specified resource.
     */
    public function ajaxShowList(Request $request)
    {
        $query = MasterFaqCategory::select([
            'ugc_faq_categories.id',
            'ugc_faq_categories.lang_id',
            'ugc_faq_categories.display_name',
            'ugc_faq_categories.category_name',
            'ugc_faq_categories.position',
            'ugc_faq_categories.status',
            'ugc_faq_categories.modified_at',
            'ugc_language.identifier as language_identifier'
        ])
        ->join('ugc_language', 'ugc_language.id', '=', 'ugc_faq_categories.lang_id');

        // Apply filters
        if ($request->filled('status')) {
            // Map "Enable" as 0, "Disable" as 1 if needed
            $query->where('ugc_faq_categories.status', $request->status);
        }

        if ($request->filled('lang_id')) {
            $query->where('ugc_faq_categories.lang_id', $request->lang_id);
        }

        if ($request->filled('display_name')) {
            $query->where('ugc_faq_categories.display_name', 'like', '%' . $request->display_name . '%');
        }

        if ($request->filled('position')) {
            $query->where('ugc_faq_categories.position', $request->position);
        }

        if ($request->filled('modified_at')) {
            $dates = explode(' - ', $request->modified_at);
            if (count($dates) === 2) {
                $query->whereBetween('ugc_faq_categories.modified_at', [
                    strtotime($dates[0]), strtotime($dates[1])
                ]);
            }
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('modified_at', function ($row) {
                return $row->modified_at 
                    ? Carbon::createFromTimestamp($row->modified_at)->format('Y-m-d H:i:s') 
                    : null;
            })
            ->editColumn('status', function($row){
                if ($row->status == 0) {
                    return 'Enable';
                } elseif ($row->status == 1) {
                    return 'Disable';
                } elseif ($row->status == 2) {
                    return 'Deleted';
                }
                return $row->status;
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="dropdown ms-auto">
                        <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-horizontal-rounded text-option"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item view-faq_category" href="#" data-id="'.$row->id.'">View</a></li>
                            <li><a class="dropdown-item edit-faq_category" href="#" data-id="'.$row->id.'">Edit</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item text-danger delete-faq_category" data-id="'.$row->id.'">Delete</a>
                            </li>
                        </ul>
                    </div>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }




    public function show($id)
    {
        $Category = MasterFaqCategory::findOrFail($id);
        return response()->json([
            'id' => $Category->id,
            'lang_id' => $Category->lang_id,
            'display_name' => $Category->display_name,
            'category_name' => $Category->category_name,
            'position' => $Category->position,
            'status' => $Category->status,
            'modified_at' => $Category->modified_at,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterFaqCategory $MasterFaqCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterFaqCategory $MasterFaqCategory)
    {
        $request->validate([
            'display_title'       => 'required|string|max:255',
            'content_language_id' => 'required|integer|exists:ugc_language,id',
            'title'               => 'required|array', // array of {content, language}
            'status'              => 'required|boolean',
        ]);

        try {
            $titlesArray = $request->title;
            if (empty($titlesArray)) {
                return response()->json([
                    'status'  => 400,
                    'message' => 'Please provide at least one language content!',
                ]);
            }

            // Convert to key-value JSON: language => content
            $titleJson = [];
            foreach ($titlesArray as $t) {
                if (!empty($t['language']) && !empty($t['content'])) {
                    $titleJson[$t['language']] = $t['content'];
                }
            }

            $MasterFaqCategory->update([
                'display_name'   => $request->display_title,
                'category_name'  => json_encode($titleJson, JSON_UNESCAPED_UNICODE),
                'lang_id'        => $request->content_language_id,
                'status'         => $request->status,
                'modified_at'    => time(),
            ]);

            return response()->json([
                'status'  => 200,
                'message' => 'Category updated successfully!',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ]);
        }
    }


    /**
     * Soft delete the specified resource.
     */
    public function destroy(MasterFaqCategory $MasterFaqCategory)
    {
        try {
            // Update status to 2 instead of deleting
            $MasterFaqCategory->update([
                'status' => 2,
                'modified_at' => time(),
            ]);

            return response()->json([
                'status'  => 200,
                'message' => 'Category deleted successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ]);
        }
    }

}
