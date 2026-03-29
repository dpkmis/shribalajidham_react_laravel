<?php

namespace App\Http\Controllers;

use App\Models\MasterLanguage;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\User;
use App\Notifications\MetaAddedNotification;
use App\Services\Breadcrumbs;

class MasterLanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Breadcrumbs::add('Dashboard', route('dashboard'));
        Breadcrumbs::add('Master Data');
        Breadcrumbs::add('Language');        
        return view('master_language.index');
    }
    

    public function store(Request $request)
    {
        $identifier = $request->title[0]['title'];
        $existingIdentifier = MasterLanguage::where('identifier', $identifier)->first();
        if ($existingIdentifier) {
            return response()->json([
                'status' => 'error',
                'message' => "This title is already exists. Please choose a different one."
            ], 400);
        }
        $request->validate([
            // 'identifier' => 'required|string|max:128',
            'title' => 'required|array',
            'status' => 'nullable|boolean',
        ]);

        $titleArray = $request->input('title');

        // Check if any of the submitted titles is 'default'
        foreach ($titleArray as $titleItem) {
            if (isset($titleItem['language']) && $titleItem['language'] === 'default') {

                // Raw query to check any existing 'default' in DB
                $existingDefaults = MasterLanguage::whereRaw(
                    "JSON_CONTAINS(title, '{\"language\": \"default\"}')"
                )->get();

                if ($existingDefaults->isNotEmpty()) {
                    $existingTitle = json_decode($existingDefaults[0]->title, true)[0]['title'] ?? 'Unknown';
                    return response()->json([
                        'status' => 'error',
                        'message' => "Only one language can be set as default. Please choose 'Optional' for the new entry or modify the existing default language first. <{$existingTitle}>"
                    ], 400);
                }
            }
        }

        // Encode and store
        $titleJson = json_encode($titleArray);

        // Save new record
        $data = new MasterLanguage();
        $data->identifier = $request->title[0]['title'];
        $data->title = $titleJson;
        $data->status = $request->status ? 1 : 0;
        
        $data->save();

        $currentUser = auth()->user();
    
        // Notify all other users
        $users = User::where('id', '<>', $currentUser->id)->where('is_deleted', 0)->get();
        foreach ($users as $user) {
            $user->notify(new MetaAddedNotification($data, $currentUser));
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Master Language has been successfully saved.'
        ]);
    }

    public function ajaxShowList(Request $request)
    {
        $query = MasterLanguage::select(['id', 'identifier', 'title', 'status', 'modified_at'])
                    ->orderBy('modified_at', 'desc'); // <-- ye line add karo

        // Apply column filters
        if ($request->has('columns')) {
            foreach ($request->get('columns') as $col) {
                $colName = $col['data'];
                $searchValue = $col['search']['value'];

                if (!empty($searchValue)) {
                    if ($colName === 'title') {
                        $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$[0].title')) LIKE ?", ["%{$searchValue}%"]);
                    } elseif ($colName === 'type') {
                        $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$[0].language')) LIKE ?", ["%{$searchValue}%"]);
                    } elseif ($colName === 'status') {
                        $query->where('status', $searchValue);
                    } elseif ($colName === 'modified_at') {
                        // Date range filter
                        $dates = explode(' - ', $searchValue);
                        if(count($dates) === 2) {
                            $start = date('Y-m-d 00:00:00', strtotime($dates[0]));
                            $end = date('Y-m-d 23:59:59', strtotime($dates[1]));
                            $query->whereBetween('modified_at', [$start, $end]);
                        }
                    } else {
                        $query->where($colName, 'like', "%{$searchValue}%");
                    }
                }
            }
        }
        return DataTables::of($query)
            ->addIndexColumn()
            ->filterColumn('modified_at', function($query, $keyword) {
                // yaha kuch bhi nahi karna because date range filter hum upar hi handle kar chuke hain
                // agar chaho to aap specific handling bhi kar sakte ho
            })
            ->editColumn('title', function ($row) {
                $titles = json_decode($row->title, true);
                return $titles[0]['title'] ?? $row->title;
            })
            ->addColumn('type', function ($row) {
                $titles = json_decode($row->title, true);
                return $titles[0]['language'] ?? '';
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="dropdown ms-auto">
                        <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-horizontal-rounded text-option"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item edit-language" href="#" data-id="'.$row->id.'">Edit</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item text-danger" onclick="deleteRecord('.$row->id.')">Delete</a>
                            </li>

                        </ul>
                    </div>
                ';
            })
            ->rawColumns(['status','action'])
            ->make(true);

    }


    public function show($id)
    {
        $language = MasterLanguage::findOrFail($id);
        return response()->json([
            'id' => $language->id,
            'identifier' => $language->identifier,
            'title' => json_decode($language->title, true)[0]['title'] ?? '',
            'type' => json_decode($language->title, true)[0]['language'] ?? '',
            'status' => $language->status,
        ]);
    }

    public function edit(MasterLanguage $masterLanguage)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            // 'identifier' => 'required|string|max:255',
            'title' => 'required|array',
            'status' => 'nullable|boolean',
        ]);

        $language = MasterLanguage::findOrFail($id);
        $identifier = $request->title[0]['title'];

        // Check for duplicate identifier excluding current record
        $existingIdentifier = MasterLanguage::where('identifier', $identifier)
                                            ->where('id', '!=', $id)
                                            ->first();
        if ($existingIdentifier) {
            return response()->json([
                'status' => 'error',
                'message' => "The identifier '{$identifier}' already exists. Please choose a different one."
            ], 400);
        }

        $languageType = strtolower(trim($request->input('language_type')));

        // Check if the selected language is default
        if ($languageType === 'default') {
            $existingDefaults = MasterLanguage::whereJsonContains('title->language', 'default')
                                ->where('id', '!=', $id)
                                ->get();

            if ($existingDefaults->isNotEmpty()) {
                // Get the first existing default language title for message
                $existingTitle = json_decode($existingDefaults->first()->title, true)[0]['title'] ?? 'Unknown';

                return response()->json([
                    'status' => 'error',
                    'message' => "Only one language can be marked as default. Please change the language <{$existingTitle}> to optional first."
                ], 400);
            }
        }

        $titleArray = $request->input('title'); // already an array like [{title: 'Hindi', language: ''}]
        $titleJson = json_encode($titleArray);

        $language->identifier = $request->title[0]['title'];
        $language->title = $titleJson;
        $language->status = $request->status ? 1 : 0;
        $language->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Master Language has been updated successfully.'
        ]);
    }

    public function destroy(Request $request, $id)
    {
        try {
            $updated = MasterLanguage::where('id', $id)->update([
                'status' => 2,
                'modified_at' => now()
            ]);
            if ($updated) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Record soft deleted successfully.'
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Record not found.'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ]);
        }
    }

}
