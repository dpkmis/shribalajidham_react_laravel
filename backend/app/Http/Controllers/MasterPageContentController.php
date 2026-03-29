<?php

namespace App\Http\Controllers;

use App\Models\MasterPageContent;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class MasterPageContentController extends Controller
{
    /**
     * Display a listing of the resource.
    */
    public function index()
    {
        return view('page_content');
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
            'display_title' => 'required|string|max:255',
            'content_language_id' => 'required|integer',
            'content' => 'required|string',
            'status' => 'required|boolean',
            'page' => 'required|string|max:255',
            'link' => 'nullable|string|max:255', // new link field
        ]);

        try {
            $MasterPageContent = MasterPageContent::create([
                'title' => $request->display_title,
                'lang_id' => $request->content_language_id,
                'description' => $request->content, // store HTML
                'status' => $request->status,
                'page' => $request->page,
                'link' => $request->link ?? null, // save link if provided
                'modified_at' => time(), // integer timestamp
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Page content added successfully!',
                'data' => $MasterPageContent
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ]);
        }
    }





    public function savePositions(Request $request)
    {
        $positions = $request->positions; // array of {id, position}

        foreach ($positions as $pos) {
            MasterPageContent::where('id', $pos['id'])->update(['position' => $pos['position']]);
        }

        return response()->json(['message' => 'Positions updated successfully!']);
    }

    /**
     * Display the specified resource.
     */
    public function ajaxShowList(Request $request)
    {
        $query = MasterPageContent::select([
            'ugc_pages.id',
            'ugc_pages.title',
            'ugc_language.identifier as language_identifier',
            'ugc_pages.status',
            'ugc_pages.position',
            'ugc_pages.modified_at'
        ])
        ->leftJoin('ugc_language', 'ugc_pages.lang_id', '=', 'ugc_language.id')
        ->orderBy('ugc_pages.position', 'asc');

        // Apply filters from request
        if ($request->filled('status')) {
            $query->where('ugc_pages.status', $request->status);
        }

        if ($request->filled('lang_id')) {
            $query->where('ugc_pages.lang_id', $request->lang_id);
        }

        if ($request->filled('title')) {
            $query->where('ugc_pages.title', 'like', '%' . $request->title . '%');
        }

        if ($request->filled('position')) {
            $query->where('ugc_pages.position', $request->position);
        }

        if ($request->filled('modified_at')) {
            $dates = explode(' - ', $request->modified_at);
            if (count($dates) === 2) {
                $start = strtotime($dates[0]);
                $end   = strtotime($dates[1]);
                $query->whereBetween('ugc_pages.modified_at', [$start, $end]);
            }
        }

        return DataTables::of($query)
            ->editColumn('modified_at', function($row){
                return date('Y-m-d H:i:s', $row->modified_at);
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
                            <li><a class="dropdown-item view-page" href="#" data-id="'.$row->id.'">View</a></li>
                            <li><a class="dropdown-item edit-page" href="#" data-id="'.$row->id.'">Edit</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item text-danger delete-page" data-id="'.$row->id.'">Delete</a>
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
        $PageContent = MasterPageContent::findOrFail($id);

        return response()->json([
            'id' => $PageContent->id,
            'title' => $PageContent->title,
            'lang_id' => $PageContent->lang_id,
            'description' => $PageContent->description,
            'status' => $PageContent->status,
            'position' => $PageContent->position,
            'modified_at' => $PageContent->modified_at,
            'link' => $PageContent->link, // added link field
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterPageContent $MasterPageContent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterPageContent $MasterPageContent)
    {
        $request->validate([
            'display_title' => 'required|string|max:255',
            'content_language_id' => 'required|integer',
            'content' => 'required|string',
            'status' => 'required|boolean',
            'page' => 'required|string|max:255',
            'link' => 'nullable|string|max:255', // new link field
        ]);

        try {
            $MasterPageContent->update([
                'title' => $request->display_title,
                'lang_id' => $request->content_language_id,
                'description' => $request->content, // update HTML
                'status' => $request->status,
                'page' => $request->page,
                'link' => $request->link ?? null, // update link if provided
                'modified_at' => time(), // integer timestamp
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Page content updated successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ]);
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterPageContent $MasterPageContent)
    {
        try {
            $MasterPageContent->update([
                'status' => 2,
                'modified_at' => time(), // store as UNIX timestamp
            ]);


            return response()->json([
                'status' => 200,
                'message' => 'Page content deleted successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ]);
        }
    }

}
