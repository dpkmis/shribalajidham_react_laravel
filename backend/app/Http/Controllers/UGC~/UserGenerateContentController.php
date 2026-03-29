<?php

namespace App\Http\Controllers\UGC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IngestedContent;
use Yajra\DataTables\DataTables;
use App\Notifications\MetaAddedNotification;
use Illuminate\Support\Facades\Auth;
use App\Services\Breadcrumbs;
use App\Services\TemplateService;
use App\Services\SqsService;
use Aws\Exception\AwsException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class UserGenerateContentController extends Controller
{
    public function index()
    {
        Breadcrumbs::add('Dashboard', route('dashboard'));
        Breadcrumbs::add('Content Management');
        Breadcrumbs::add('Creator Content');
        return view('ugc.creatore_content.index');
    }

    public function ajaxShowList(Request $request)
    {
        $query = IngestedContent::with(['category', 'contentLangauge', 'region'])
            ->select(['id', 'content_type', 'title', 'data_state', 'language', 'category_id', 'channel_id', 'user_id', 'vc_status', 'modified_at'])
            ->where('is_deleted', 0)
            ->where('data_state', '<>', 'draft');
        // Apply column filters
        if ($request->has('columns')) {
            foreach ($request->get('columns') as $col) {
                $colName = $col['data'];
                $searchValue = $col['search']['value'];

                if (!empty($searchValue)) {
                    if ($colName === 'modified_at') {
                        // Date range filter
                        $dates = explode(' - ', $searchValue);
                        if (count($dates) === 2) {
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
            ->filterColumn('modified_at', function ($query, $keyword) {
                // yaha kuch bhi nahi karna because date range filter hum upar hi handle kar chuke hain
                // agar chaho to aap specific handling bhi kar sakte ho
            })
            // ->editColumn('title', function ($row) {
            //     $titles = json_decode($row->title, true);
            //     return $titles[0]['title'] ?? $row->title;
            // })
            // ->addColumn('type', function ($row) {
            //     $titles = json_decode($row->title, true);
            //     return $titles[0]['language'] ?? '';
            // })
            ->addColumn('action', function ($row) {
                return '
                    <div class="dropdown ms-auto">
                        <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-horizontal-rounded text-option"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <a class="dropdown-item" href="ugc-creator-content/view/' . $row->id . '">View</a>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item text-danger" onclick="deleteRecord(' . $row->id . ')">Delete</a>
                            </li>

                        </ul>
                    </div>
                ';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);

    }

    public function view(Request $request)
    {
        Breadcrumbs::add('Dashboard', route('dashboard'));
        Breadcrumbs::add('Content Management');
        Breadcrumbs::add('Creator Content', route('ugc.user_generate_content'));
        Breadcrumbs::add('View Creator Content');

        $ingestData = IngestedContent::with(['category', 'contentLangauge', 'region'])
            ->where('is_deleted', 0)
            ->where('id', $request->id)
            ->first();
        // dd($ingestData);             
        return view('ugc.creatore_content.detail', compact('ingestData'));
    }

    public function playVideo()
    {
        if ($_POST['vc_id']) {

            $accesskey = env('VC_ACCESS_KEY_ENCODED');
            $secret = env('VC_SECRET_KEY_ENCODED');

            $header = array();
            $header[] = "Cache-Control:no-cache";
            $header[] = "Device-Type:1";
            $post_data['name'] = $_POST['vc_id'];
            $header[] = "accessKey:" . $accesskey;
            $header[] = "secretKey:" . $secret;
            $header[] = "Account-Id:" . env('VC_ACCOUNT_ID');
            $header[] = "User-Id:" . AUTH::id();
            $header[] = "Device-Id:1";
            $header[] = "Version:1";
            $header[] = "Device-Name:Admin";
            $post_data['Device-Name'] = 'Admin';
            $post_data['flag'] = '1';

            $apiUrl = env('VC_API_ENDPOINT') . '/getVideoDetailsDrm';

            $data = TemplateService::file_curl_content($apiUrl, $post_data, $header);

            if (isset($data['status']) && $data['status'] == true) {
                $api_data = $data['data']['link'];

                if (isset($api_data['trick-play-settings']['url'])) {
                    $newUrl = str_replace('trick_play_images.zip', '', $api_data['trick-play-settings']['url']);
                    $api_data['trick-play-settings']['url'] = $newUrl . "Thumbnail_{index}.jpg";
                }

                return json_encode(
                    array(
                        'data' => 1,
                        'url' => $api_data['file_url'],
                        'type' => 'mpd',
                        'token' => $api_data['token'],
                        'trick-play-settings' => $api_data['trick-play-settings'] ?? ''
                    )
                );

            } else {
                return json_encode(['status' => false, 'message' => 'url not found', 'data' => []]);

            }
        }
    }


    public function data_approval_system(Request $request, IngestedContent $ingestedContent, SqsService $sqsService)
    {
        // Step 1: Validate request
        // Validate incoming request
        $request->validate([
            'id' => 'required',
            'data_status' => 'required'
        ]);

        try {
            // Step 2: Fetch content safely
            $ingestedContent = IngestedContent::where('id', $request->id)->first();

            DB::table('ugc_data_ingestion')
            ->where('id', $request->id)
            ->update([
                'manually_passed' => $request['data_status'],
                'comment'         => $request['data_comment'] ?? null,
                'modified_at'     => now(),
            ]);

                       
            // Step 4: Prepare payload for SQS
            $payload = [
                'videoId' => $ingestedContent->video_id,
                'event'   => 'video.manuallyApproved',                
            ];

            // Step 5: Try sending to SQS (or log error)
            $sqsResponse = $sqsService->sendMessage($payload);

            Log::info('✅ Data approval updated and SQS message sent', [
                'video_id'   => $ingestedContent->video_id,
                'payload'    => $payload,
                'sqs_status' => $sqsResponse ?? 'queued',
            ]);

            return response()->json([
                'status'  => 200,
                'message' => 'Data status updated successfully!',
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('⚠️ Ingested content not found', ['id' => $request['id']]);
            return response()->json([
                'status'  => 404,
                'message' => 'Record not found.',
            ], 404);

        } catch (AwsException $e) {
            Log::error('❌ AWS SQS failed', [
                'error' => $e->getAwsErrorMessage(),
                'payload' => $payload ?? [],
            ]);
            return response()->json([
                'status'  => 500,
                'message' => 'Update saved, but failed to push to queue.',
            ], 500);

        } catch (\Exception $e) {
            Log::error('❌ General Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'status'  => 500,
                'message' => 'Something went wrong, please try again later.',
            ], 500);
        }
    }

    
}
