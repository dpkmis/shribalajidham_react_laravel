<?php

namespace App\Http\Controllers;

use App\Models\IngestedContent;
use App\Models\VideoModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;



class HomeController extends Controller
{

    public function index(Request $request)
    {                       
        return view('dashboard');
    }


    public function getContent(Request $request)
    {
        if ($request->ajax()) {                       
            $ingestedContents = IngestedContent::with(['publishVideo', 'contentLangauge', 'region'])
            ->select('ugc_data_ingestion.*')
            ->where('is_deleted', 0)            
            ->where('data_state', '<>', 'draft')
            ->get();
            return DataTables::of($ingestedContents)
                ->addIndexColumn()
                ->addColumn('content_language', function ($video) {
                    return $video->contentLangauge && $video->contentLangauge->identifier
                    ? $video->contentLangauge->identifier
                    : '-';
                }) 
                ->addColumn('total_view', function ($views) {
                    return $views->publishVideo && $views->publishVideo->views_count
                    ? $views->publishVideo->views_count
                    : '-';
                }) 
                ->addColumn('region', function ($region_data) {
                    return $region_data->region && $region_data->region->state
                    ? $region_data->region->state
                    : '-';
                }) 
               ->addColumn('data_state', function ($row) {
                    // Example: map DB value to custom label
                    switch ($row->data_state) {
                        case 'in_progress':
                            return '<div class="badge rounded-pill text-warning bg-light-warning p-2 text-uppercase px-3 __web-inspector-hide-shortcut__"><i class="bx bxs-circle me-1"></i>In-progress</div>';
                        case 'active':
                            return '<div class="badge rounded-pill text-success bg-light-success p-2 text-uppercase px-3 __web-inspector-hide-shortcut__"><i class="bx bxs-circle me-1"></i>Active</div>';
                        case 'in_active':
                            return '<div class="badge rounded-pill text-danger bg-light-danger p-2 text-uppercase px-3 __web-inspector-hide-shortcut__"><i class="bx bxs-circle me-1"></i>In-active</div>';
                        case 'rejected':
                            return '<div class="badge rounded-pill text-danger bg-light-danger p-2 text-uppercase px-3 __web-inspector-hide-shortcut__"><i class="bx bxs-circle me-1"></i>Rejected</div>';
                        case 'pending':
                            return '<div class="badge rounded-pill text-warning bg-light-warning p-2 text-uppercase px-3 __web-inspector-hide-shortcut__"><i class="bx bxs-circle me-1"></i>Pending</div>';   
                        case 'approved':
                            return '<div class="badge rounded-pill text-success bg-light-success p-2 text-uppercase px-3 __web-inspector-hide-shortcut__"><i class="bx bxs-circle me-1"></i>Approved</div>';
                        default:
                            return '<div class="badge rounded-pill text-secondary bg-light-secondary p-2 text-uppercase px-3 __web-inspector-hide-shortcut__"><i class="bx bxs-circle me-1"></i>Unknown</div>';
                    }
                })    
                // ->rawColumns(['data_state'])  
                ->addColumn('action', function ($row) {
                    $btn  = '<a href="" class="btn btn-sm btn-primary">View</a> ';
                    $btn .= '<a href="" class="btn btn-sm btn-warning">Edit</a> ';
                    $btn .= '<button type="button" data-id="'.$row->id.'" class="btn btn-sm btn-danger delete-btn">Delete</button>';
                    return $btn;
                })
                ->rawColumns(['data_state','action'])      
                ->editColumn('created_at', function ($ingestedContent) {
                    return $ingestedContent->created_at->format('d M Y');
                })
                ->make(true);
        }
    } 
    
    public function getDraftContent(Request $request)
    {
        if ($request->ajax()) {                       
            $ingestedContents = IngestedContent::with(['publishVideo', 'contentLangauge', 'region'])
            ->select('ugc_data_ingestion.*')
            ->where('is_deleted', 0)            
            ->where('data_state', 'draft')
            ->get();
            return DataTables::of($ingestedContents)
                ->addIndexColumn()
                ->addColumn('content_language', function ($video) {
                    return $video->contentLangauge && $video->contentLangauge->identifier
                    ? $video->contentLangauge->identifier
                    : '-';
                }) 
                ->addColumn('total_view', function ($views) {
                    return $views->publishVideo && $views->publishVideo->views_count
                    ? $views->publishVideo->views_count
                    : '-';
                }) 
                ->addColumn('region', function ($region_data) {
                    return $region_data->region && $region_data->region->state
                    ? $region_data->region->state
                    : '-';
                }) 
               ->addColumn('data_state', function ($row) {
                    // Example: map DB value to custom label
                    switch ($row->data_state) {
                        case 'draft':
                            return '<div class="badge rounded-pill text-warning bg-light-warning p-2 text-uppercase px-3 __web-inspector-hide-shortcut__"><i class="bx bxs-circle me-1"></i>Draft</div>';      
                        default:
                            return '<div class="badge rounded-pill text-secondary bg-light-secondary p-2 text-uppercase px-3 __web-inspector-hide-shortcut__"><i class="bx bxs-circle me-1"></i>Unknown</div>';
                    }
                })    
                // ->rawColumns(['data_state'])  
                ->addColumn('action', function ($row) {
                    $btn  = '<a href="" class="btn btn-sm btn-primary">View</a> ';
                    $btn .= '<a href="" class="btn btn-sm btn-warning">Edit</a> ';
                    $btn .= '<button type="button" data-id="'.$row->id.'" class="btn btn-sm btn-danger delete-btn">Delete</button>';
                    return $btn;
                })
                ->rawColumns(['data_state','action'])      
                ->editColumn('created_at', function ($ingestedContent) {
                    return $ingestedContent->created_at->format('d M Y');
                })
                ->make(true);
        }
    }
    
    public function getTopStates(Request $request)
    {
         if ($request->ajax()) {                       
            
           $range = $request->get('range', 'weekly'); // day/week/month

           if ($range === 'weekly') {
                $dateFormat = "%Y-%m-%d";   // Group by date
                $phpFormat  = "Y-m-d";
                $fromDate   = Carbon::now()->subDays(6)->startOfDay(); // today + last 6 days = 7 days
            } elseif ($range === 'monthly') {
                $dateFormat = "%Y-%m-%d";   // Still group by date (daily trend over 30 days)
                $phpFormat  = "Y-m-d";
                $fromDate   = Carbon::now()->subDays(29)->startOfDay(); // today + last 29 days = 30 days
            } else { // yearly
                $dateFormat = "%Y-%m";      // Group by month
                $phpFormat  = "Y-m";
                $fromDate   = Carbon::now()->subMonths(11)->startOfMonth(); // this month + last 11 months = 12 months
            }


            $ingestedContents = IngestedContent::with(['publishVideo', 'contentLangauge', 'region'])
            ->select('ugc_data_ingestion.*')
            ->where('is_deleted', 0)            
            ->where('data_state', '<>', 'draft')
            ->get();

            $topStates = VideoModel::join('ugc_creators as region', 'ugc_videos.channel_id', '=', 'region.channel_id')
                ->where('ugc_videos.is_deleted', 0)
                ->where('ugc_videos.c_status', 0)
                ->where('ugc_videos.created_at', '>=', $fromDate)
                ->select(
                    'region.state',
                    \DB::raw('COUNT(ugc_videos.id) as total_contents')
                )
                ->groupBy('region.state')
                ->orderByDesc('total_contents')
                ->get();


            return response()->json(['data' => $topStates]);

        }
    }

    public function getReviewedContents(Request $request)
    {
        $range = $request->get('range', 'weekly'); // weekly / monthly / yearly

        if ($range === 'weekly') {
            $fromDate   = now()->subDays(6)->startOfDay(); // Last 7 days
            $dateFormat = "%Y-%m-%d"; // group by day
        } elseif ($range === 'monthly') {
            $fromDate   = now()->subDays(29)->startOfDay(); // Last 30 days
            $dateFormat = "%Y-%m-%d"; // still group by day
        } else { // yearly
            $fromDate   = now()->subMonths(11)->startOfMonth(); // Last 12 months
            $dateFormat = "%Y-%m"; // group by month
        }

        $data = IngestedContent::selectRaw("
                DATE_FORMAT(created_at, '{$dateFormat}') as period,
                SUM(CASE WHEN is_ai_passed = 0 THEN 1 ELSE 0 END) as flagged,
                SUM(CASE WHEN is_ai_passed = 1 THEN 1 ELSE 0 END) as passed
            ")
            ->where('is_deleted', 0)
            ->where('created_at', '>=', $fromDate)
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        // Prepare arrays for Chart.js
        $labels  = $data->pluck('period');
        $flagged = $data->pluck('flagged');
        $passed  = $data->pluck('passed');

        return response()->json([
            'labels'  => $labels,
            'flagged' => $flagged,
            'passed'  => $passed,
            'range'   => ucfirst($range),
        ]);
    }






}
