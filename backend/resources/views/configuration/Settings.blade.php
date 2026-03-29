<x-app-layout>
    <div class="row">
        <div class="col-md-4">
            <div class="card p-4">
                <div class="card-body">
                    <h4>Meta Settings</h4>
                    <hr>
                    <form method="POST" id="like_settings" action="{{ route('setting.storeData') }}">
                        @csrf
                        <div class="d-flex align-items-center gap-3">
                            <div class="form-check form-switch col-md-3">
                                <input type="hidden" name="IS_LIKE" value="0">
                                <input class="form-check-input" type="checkbox" role="switch" name="IS_LIKE" value="1"
                                    id="flexSwitchCheckDefault1" {{ $configuration_data['IS_LIKE'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="flexSwitchCheckDefault1">Like</label>
                            </div>

                            <div class="form-check form-switch col-md-3">
                                <input type="hidden" name="IS_DISLIKE" value="0">
                                <input class="form-check-input" type="checkbox" role="switch" name="IS_DISLIKE"
                                    value="1" id="flexSwitchCheckDefault2" {{ $configuration_data['IS_DISLIKE'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="flexSwitchCheckDefault2">DisLike</label>
                            </div>

                            <div class="form-check form-switch col-md-3">
                                <input type="hidden" name="IS_VIEW" value="0">
                                <input class="form-check-input" type="checkbox" role="switch" name="IS_VIEW" value="1"
                                    id="flexSwitchCheckDefault3" {{ $configuration_data['IS_VIEW'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="flexSwitchCheckDefault3">View</label>
                            </div>
                        </div>
                        <div class="form-group text-end">
                            <button type="submit" class="btn btn-primary px-4">Submit</button>
                        </div>
                    </form>
                    <span class="text-secondary"><i class="fa fa-info-circle" aria-hidden="true"></i><strong>
                            Note:</strong><i>These settings are related to Meta Data.</i></span>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card p-4">
                <h4>Content Settings</h4>
                <hr>
                <form method="POST" id="video_max_upload" action='{{ route('setting.storeData') }}'>
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Video Max Size<span> (in MB)</span></label>
                            <input type="text" class="form-control" name="VIDEO_MAX_UPLOAD_SIZE"
                                value="{{ $configuration_data['VIDEO_MAX_UPLOAD_SIZE'] ?? '' }}">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Channel Max Storage<span> (in GB)</span></label>
                            <input type="text" class="form-control" name="CHANNEL_MAX_UPLOAD_SIZE"
                                value="{{ $configuration_data['CHANNEL_MAX_UPLOAD_SIZE'] ?? '' }}">
                        </div>

                        <div class="form-group text-end " style="margin-top: 10px">
                            <button type="submit" class="btn btn-primary px-4" id="submitBtn">Submit</button>
                        </div>
                    </div>
                </form>
                <span class="text-secondary"><i class="fa fa-info-circle" aria-hidden="true"></i><strong>
                        Note:</strong><i>These settings are related to Content.</i></span>

            </div>
        </div>
    </div>
    @push('scripts')
        <script>
        </script>
    @endpush
</x-app-layout>