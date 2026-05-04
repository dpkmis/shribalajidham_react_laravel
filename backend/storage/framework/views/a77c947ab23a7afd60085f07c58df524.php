<?php if(!empty($breadcrumbs = \App\Services\Breadcrumbs::get())): ?>
    <!--breadcrumb-->
    <?php        
        $titles = array_map(fn($crumb) => $crumb['title'], $breadcrumbs);
    ?>
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3"><?php echo e(last($titles)); ?></div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <?php $__currentLoopData = $breadcrumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $crumb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($loop->last): ?>
                            <li class="breadcrumb-item"><a href="javascript:;"><?php echo e($crumb['title']); ?></a></li>
                        <?php else: ?>
                            <li class="breadcrumb-item text-secondary" aria-current="page">
                                <a href="<?php echo e($crumb['url'] ?? '#'); ?>"><?php echo e($crumb['title']); ?></a>
                            </li>
                        <?php endif; ?>    
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ol>
            </nav>
        </div>      
    </div>
    <!--end breadcrumb-->
<?php endif; ?><?php /**PATH /var/www/html/shribalajidham_react_laravel/backend/resources/views/components/breadcrumbs.blade.php ENDPATH**/ ?>