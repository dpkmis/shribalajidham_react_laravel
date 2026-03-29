<script src="{{ asset('assets/plugins/apexcharts-bundle/js/apexcharts.min.js') }}"></script>
<x-app-layout>
    <x-slot name="header">
        @push('styles')
            <style>
                .table-responsive {
                    overflow-y: auto;
                    overflow-x: auto;
                }
                .select2-container--bootstrap-5 .select2-dropdown .select2-results__options:not(.select2-results__options--nested) {
                    overflow-x: none!important;
                }
            </style>
        @endpush
        <div class="page-wrapper">
            <div class="page-content totalDataNum">

                <!-- Summary Cards -->
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    <div class="col">
                        <div class="card radius-10 overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <p class="mb-0">Total Published Videos</p>
                                        <h5 class="mb-0" id="totalVideosCount"></h5>
                                    </div>
                                    <div class="ms-auto"><i class='bx bx-cart font-30'></i></div>
                                </div>
                            </div>
                            <!-- <div id="w-chart5"></div> -->
                        </div>
                    </div>

                    <div class="col">
                        <div class="card radius-10 overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div>
                                        <p class="mb-0">Total Users</p>
                                        <h5 class="mb-0" id="totalUsersCount"></h5>
                                    </div>
                                    <div class="ms-auto"><i class='bx bx-wallet font-30'></i></div>
                                </div>
                            </div>
                            <!-- <div id="w-chart6"></div> -->
                        </div>
                    </div>

                    <div class="col">
                        <div class="card radius-10 overflow-hidden">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div>
                                        <p class="mb-0">Total Flagged Content</p>
                                        <h5 class="mb-0" id="totalFlaggedCount"></h5>
                                    </div>
                                    <div class="ms-auto"><i class='bx bx-chat font-30'></i></div>
                                </div>
                            </div>
                            <!-- <div id="w-chart8"></div> -->
                        </div>
                    </div>
                </div>

               
            </div>
        </div>
        @push('scripts')
            
        @endpush
    </x-slot>
</x-app-layout>