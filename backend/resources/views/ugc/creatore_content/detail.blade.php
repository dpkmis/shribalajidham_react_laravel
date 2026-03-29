<x-app-layout>
    <link rel="stylesheet" href="{{ asset('assets/shakaplayer/css/controls.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/shakaplayer/css/video-js.css') }}">

    @push('style')  

    <style>
        .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        background-color: #fff;
        }

        .section-title {
        font-size: 1rem;
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 6px;
        margin-bottom: 1rem;
        }

        .info-label {
        font-weight: 500;
        color: #6c757d;
        }

        .thumbnail {
        width: 100%;
        border-radius: 8px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .thumbnail:hover {
        transform: scale(1.03);
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }

        .approval-card {
        position: sticky;
        top: 1rem;
        }

        .badge-status {
        font-size: 0.85rem;
        padding: 0.4em 0.7em;
        }

        textarea {
        resize: none;
        }

        .form-select, .form-control {
        border-radius: 8px;
        }
    </style>
    
    @endpush
    <div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
                <div class="card p-4">
                    <div class="row g-3">
                        @php
                            $detailedIngesteddata = null;
                            if (!empty($ingestData->data_json)) {
                                $detailedIngesteddata = json_decode($ingestData->data_json);
                            }
                        @endphp                
                        {{-- ========== BASIC INFO ========== --}}
                        <div class="col-12">
                            <h6 class="section-title">Basic Information</h6>
                            <hr>
                        </div>
                        
                        <x-display-field label="User ID" :value="$ingestData->user_id ?? ''" col="col-md-6"  />
                        <x-display-field label="Video ID" :value="$detailedIngesteddata->video_id ?? ''" col="col-md-6" />
                        <x-display-field label="Category" :value="optional($ingestData->category)->display_title ?? ''" col="col-md-6" type="tag" user_class="text-primary bg-light-primary" />
                        <x-display-field label="Genre" :value="optional($ingestData->genre)->display_title ?? ''" col="col-md-6" type="tag" user_class="text-success bg-light-success" />
                        <x-display-field label="Language" :value="optional($ingestData->content_langauge)->identifier ?? ''" col="col-md-6" type="tag" user_class="text-info bg-light-info" />
                        <x-display-field label="Channel (Region)" :value="optional($ingestData->region)->channel_name ?? ''" col="col-md-6" />
                    </div>
                </div>
                <div class="card p-4">   
                    <div class="row g-3">                    
                        {{-- ========== CONTENT DETAILS ========== --}}
                        <div class="col-12">    
                            <h6 class="section-title">Content Details</h6>
                            <hr>
                        </div>

                        <x-display-field label="Content Type" :value="$ingestData->content_type ?? ''" col="col-md-3" />
                        <x-display-field label="Media Type" :value="$ingestData->media_type ?? ''" col="col-md-3" />
                        <x-display-field label="Title" :value="$ingestData->title ?? ''" col="col-md-3" />
                        <x-display-field label="Tags" :value="$detailedIngesteddata->tags ?? ''" col="col-md-3" type="tag" user_class="text-primary bg-light-primary" />
                        
                        <x-display-field label="Description" :value="$detailedIngesteddata->description ?? '' " col="col-md-12" type="textarea" />

                        <x-display-field label="Visibility" :value="$detailedIngesteddata->visibility ?? ''" col="col-md-3" type="tag" user_class="text-dark bg-light-warning"  />
                        <x-display-field label="Made For Kids" :value="$detailedIngesteddata->made_for_kids ??''" col="col-md-3" type="tag" user_class="text-info bg-light-info"/>
                        <x-display-field label="Allow Comments" :value="$detailedIngesteddata->allow_comments ??''" col="col-md-3" type="tag" user_class="text-info bg-light-info"   />
                        <x-display-field label="Terms Accepted" :value="$detailedIngesteddata->terms_accepted ??''"  col="col-md-3" type="tag" user_class="text-success bg-light-success"  />
                        <x-display-field label="Allow Subtitile" :value="$detailedIngesteddata->allow_subtitles ??''" col="col-md-3" type="tag" user_class="text-danger bg-light-danger"   />
                    </div>
                </div>
            <div class="card p-4">  
                <div class="row g-3">                                                 
                    {{-- ========== STATUS INFO ========== --}}
                    <div class="col-12">
                        <h6 class="section-title">Status Information</h6>
                        <hr>
                    </div>

                    <x-display-field 
                        label="Data State" 
                        :value="!empty($ingestData->data_state) ? \Illuminate\Support\Str::title(str_replace('_', ' ', $ingestData->data_state)) : ''" 
                    col="col-md-4" type="tag" user_class="text-primary bg-light-primary" />

                    <x-display-field label="AI Job ID" :value="$ingestData->ai_job_id ?? ''" col="col-md-4" type="tag" user_class="text-success bg-light-success" />
                    <x-display-field label="AI Passed" :value="($ingestData->is_ai_passed == 0) ? 'Failed' : 'Passed'" col="col-md-4" type="tag" user_class="text-primary bg-light-primary" />

                   @if($ingestData->transcoding_type == 0)
                        <x-display-field 
                            label="Proxy Transcoding Status" 
                            :value="$ingestData->p_transcode_status ?? ''" 
                        />
                    @else
                        <x-display-field 
                            label="Transcoding Status" 
                            :value="$ingestData->f_transcode_status ?? ''" 
                        />
                    @endif

                    @php
                        $transcodingLabels = [
                            1 => 'Proxy Generated',
                            2 => 'AI Processed',
                            3 => 'Transcode Started',
                            4 => 'Transcoded',
                            5 => 'Failed',
                        ];

                        // Get numeric status from vc_status
                        $vcStatus = $ingestData->vc_status ?? null;

                        // Map numeric status to label
                        $vcLabel = $vcStatus && isset($transcodingLabels[$vcStatus])
                            ? $transcodingLabels[$vcStatus]
                            : '';
                    @endphp

                    <x-display-field label="VC Status" :value="$vcLabel" col="col-md-4" type="tag" user_class="text-primary bg-light-primary" />
                </div>
                </div>
                {{-- ========== SCHEDULING ========== --}}                    
                @if($ingestData->scheduled_at)
                    <div class="card p-4">
                        <div class="row g-3">                    
                            <div class="col-12">
                                <h6 class="section-title">Scheduling</h6>
                                <hr>
                            </div>                    
                            <x-display-field label="Scheduled At" :value="optional($ingestData->scheduled_at)->format('Y-m-d H:i:s')" />                 
                        </div>
                    </div>
                @endif
                @if(!empty($detailedIngesteddata->thumbnail_url))
                    <div class="card p-4">                   
                        <div class="row g-3">                    
                            <div class="col-12">
                                <h6 class="section-title">Thumbnails</h6>
                                <hr>
                            </div>     
                            <div class="row">
                                @foreach($detailedIngesteddata->thumbnail_url as $image)
                                    <div class="col-md-2 mb-2">
                                        <img src="{{ $image }}" class="img-fluid rounded" alt="Thumbnail">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif                
        </div>
        
        <div class="col-md-4">
            <div class="card p-4">
                <div id="video_player_html_active">
                    <video id="my-video" class="video-js w-100">
                    </video>
                </div>            
                @if($ingestData->is_ai_passed == '1')                    
                    <div class="row g-3 mt-3">
                        <h5>Manually Approval</h5>
                        <form method="POST" id="data_approval_form">
                            @csrf
                            <input type="hidden" name="id" value="{{ $ingestData->id }}">

                            <div class="form-group">
                                <label class="form-label fw-semibold">Is Manually Approve</label>
                                <select class="form-control" name="data_status" id="data_status">
                                    <option value="">Select Option</option>
                                    <option value="1">Approve</option>
                                    <option value="0">Reject</option>
                                </select>
                            </div>

                            <div class="form-group mt-2" id="comment_wrapper" style="display: none;">
                                <label>Comment <span class="text-danger">*</span></label>
                                <textarea rows="3" class="form-control" placeholder="Add approval notes..." name="data_comment" id="data_comment"></textarea>
                            </div>

                            <div class="mt-1 text-end">
                                <button type="submit" class="btn btn-primary px-4">Submit</button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
            </div>
        </div>
    </div>
    </div>

    @push('scripts')
        
        <script type="text/javascript" src="{{ asset('assets/shakaplayer/js/player.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/shakaplayer/js/shaka.ui.js') }}"></script>
        <script type="text/javascript">
            $(window).ready(function() {
                var vc_id = '{{ $ingestData->transcoding_type == '0' ? $ingestData->proxy_vdc_id : $ingestData->final_vdc_id }}'  ;   
                       
                $.ajax({
                    type: 'POST',
                    url: "{{ route('ugc.play_video') }}",
                    dataType: 'json',
                    data: {
                        _token: "{{ csrf_token() }}",
                        vc_id: vc_id,
                    },
                    success: async function(data) {
                        let dashUri = data.url;
                        let toke_data = data.token;
                        let videoTagId = "my-video";
                        let videoTagParentId = "video_player_html_active";


                        var arr = {
                            videoTagId: videoTagId,
                            videoTagParentId: videoTagParentId,
                            token_data: toke_data,
                            dashUri: dashUri,
                            pallyconToken: 'pallycon-customdata-v2',
                            licenceURL: 'https://license.videocrypt.com/validateLicense'
                        };

                        await playerFunction(arr);
                    }
                });
            })

            $(".vidLanguage, #typeMarker, #audioBitrate, #videoBitrate, #thumnailType, #selectRole, #selectUser,#cerAge,#cerCountry,#cerRating,#cerCountryTwo").select2({
                placeholder: "Please Select",
                allowClear: true,
                minimumResultsForSearch: -1,
                allowClear: false,
                dropdownAutoWidth: true,
                width: '100px'
            });

            $(document).on('submit','#data_approval_form',function (e){
                e.preventDefault();
                $('.error_validation').hide();
                var err = 0;

                if($("select[name='data_status']").val() == ""){
                    $("select[name='data_status']").parent(".form-group").append("<span class='text-danger error_validation error'>Please select option</span>");
                    err++;
                }
                if($("select[name='data_status']").val() == "0"){
                    if($("textarea[name='data_comment']").val() == ""){
                        $("textarea[name='data_comment']").parent(".form-group").append("<span class='text-danger error_validation error'>Please enter comment</span>");
                        err++;
                    }
                }
                
                if(err != 0){
                    return false;
                }

                var formData = new FormData($('#data_approval_form')[0]);

                $.ajax({
                    url: "{{ route('ugc.data_approval') }}",
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        // console.log('Success:', response);
                        success_noti(response.message);
                    },
                    error: function (xhr, status, error) {
                        console.error('Error:', error);
                    }
                });                
            });
        </script>


        <script>
        $(document).ready(function () {
            const $status = $('#data_status');
            const $commentWrapper = $('#comment_wrapper');
            const $comment = $('#data_comment');
            const $form = $('#data_approval_form');

            // Handle dropdown change
            $status.on('change', function () {
                if ($(this).val() === '0') {
                    $commentWrapper.slideDown(200);
                    $comment.attr('required', true);
                } else {
                    $commentWrapper.slideUp(200);
                    $comment.removeAttr('required').val('');
                }
            });

            // Validate before submit
            $form.on('submit', function (e) {
                if ($status.val() === '0' && $.trim($comment.val()) === '') {
                    e.preventDefault();
                    alert('Please enter a comment when rejecting.');
                    $comment.focus();
                }
            });
        });
        </script>


    @endpush
</x-app-layout>