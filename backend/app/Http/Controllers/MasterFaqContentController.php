<?php

namespace App\Http\Controllers;

use App\Models\MasterFaqContent;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class MasterFaqContentController extends Controller
{
    public function index()
    {
        $languages = DB::table('ugc_language')->select('id', 'identifier')->get();
        $categories = DB::table('ugc_faq_categories')
            ->select('id', 'display_name as category_name')
            ->orderBy('position', 'asc')
            ->get();

        return view('master_FaqContent.index', compact('languages', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Passing a flag to tell the view to open modal
        return view('master_FaqContent.index', ['openModal' => true]);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|integer|exists:ugc_faq_categories,id',
            'display_name' => 'required|string|max:255',
            'title' => 'required|array',
            'description' => 'required|array',
            'status' => 'nullable|boolean',
        ]);
        $faq = new MasterFaqContent();
        $faq->category_id = $request->category_id;
        $faq->display_name = $request->display_name;
        $faq->title = json_encode($request->title, JSON_UNESCAPED_UNICODE);
        $faq->description = json_encode($request->description, JSON_UNESCAPED_UNICODE);
        $faq->status = $request->status ? 1 : 0;
        $faq->created_by = auth()->id() ?? 0;
        $faq->created_at = time();
        $faq->modified_at = time();
        $faq->save();

        return response()->json([
            'status' => 'success',
            'message' => 'FAQ Content has been successfully saved.'
        ]);
    }




    /**
     * Display the specified resource.
     */

    public function ajaxShowList(Request $request)
    {
        $query = MasterFaqContent::select([
                        'ugc_faq_content.id',
                        'ugc_faq_content.category_id',
                        'ugc_faq_categories.display_name as category_name',
                        'ugc_faq_content.display_name',
                        'ugc_faq_content.title',
                        'ugc_faq_content.description',
                        'ugc_faq_content.status',
                        'ugc_faq_content.modified_at'
                    ])
                    ->leftJoin('ugc_faq_categories', 'ugc_faq_categories.id', '=', 'ugc_faq_content.category_id')
                    ->orderBy('ugc_faq_content.modified_at', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '
                    <div class="dropdown ms-auto">
                        <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-horizontal-rounded text-option"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item view-faq" href="#" data-id="'.$row->id.'">View</a></li>
                            <li><a class="dropdown-item edit-faq" href="#" data-id="'.$row->id.'">Edit</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item text-danger delete-faq_content" data-id="'.$row->id.'">Delete</a>
                            </li>
                        </ul>
                    </div>
                ';
            })
            // Fix filter for category_name alias
            ->filterColumn('category_name', function($query, $keyword){
                if($keyword !== "") {
                    // Compare by category_id instead of name
                    $query->where('ugc_faq_content.category_id', $keyword);
                }
            })

            // Fix filter for status column
            ->filterColumn('status', function($query, $keyword){
                // Only apply filter if keyword is not empty
                if ($keyword !== "") {
                    // Convert string to integer explicitly
                    $status = intval($keyword);
                    $query->where('ugc_faq_content.status', $status);
                }
            })

            ->rawColumns(['status','action'])
            ->make(true);
    }


    public function show($id)
    {
        $faq = MasterFaqContent::findOrFail($id);

        return response()->json([
            'id' => $faq->id,
            'display_name' => $faq->display_name,
            'category_id' => $faq->category_id,
            'status' => $faq->status,
            'faq_content' => collect(json_decode($faq->title, true))->map(function($question, $lang) use ($faq) {
                return [
                    'title_lang' => $lang,
                    'title' => $question,
                    'desc_lang' => $lang,
                    'description' => json_decode($faq->description, true)[$lang] ?? ''
                ];
            })->values()
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterFaqContent $MasterFaqContent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required|integer|exists:ugc_faq_categories,id',
            'display_name' => 'required|string|max:255',
            'title' => 'required|array',
            'description' => 'required|array',
            'status' => 'nullable|boolean',
        ]);

        $faq = MasterFaqContent::findOrFail($id);

        $faq->category_id = $request->category_id;
        $faq->display_name = $request->display_name;
        $faq->title = json_encode($request->title, JSON_UNESCAPED_UNICODE);
        $faq->description = json_encode($request->description, JSON_UNESCAPED_UNICODE);
        $faq->status = $request->status ? 1 : 0;
        $faq->modified_at = time();
        $faq->save();

        return response()->json([
            'status' => 'success',
            'message' => 'FAQ Content has been successfully updated.'
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterFaqContent $MasterFaqContent)
    {
        try {
            // Update status to 2 instead of deleting
            $MasterFaqContent->update([
                'status' => 2
            ]);

            return response()->json([
                'status'  => 200,
                'message' => 'FAQ Category deleted successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ]);
        }
    }
}
