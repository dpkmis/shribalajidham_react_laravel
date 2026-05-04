<script src="<?php echo e(asset('assets/plugins/apexcharts-bundle/js/apexcharts.min.js')); ?>"></script>
<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <?php $__env->startPush('styles'); ?>
            <style>
                .table-responsive {
                    overflow-y: auto;
                    overflow-x: auto;
                }
                .select2-container--bootstrap-5 .select2-dropdown .select2-results__options:not(.select2-results__options--nested) {
                    overflow-x: none!important;
                }
            </style>
        <?php $__env->stopPush(); ?>
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
        <?php $__env->startPush('scripts'); ?>
            
        <?php $__env->stopPush(); ?>
     <?php $__env->endSlot(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH /var/www/html/shribalajidham_react_laravel/backend/resources/views/dashboard.blade.php ENDPATH**/ ?>