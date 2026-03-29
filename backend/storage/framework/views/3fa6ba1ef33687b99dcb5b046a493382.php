<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'WavesTube')); ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- favicon -->
    <link rel="icon" href="<?php echo e(asset('fabIcon.svg')); ?>" type="image/png" />

    <!-- plugins -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/notifications/css/lobibox.min.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/simplebar/css/simplebar.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/metismenu/css/metisMenu.min.css')); ?>" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/bootstrap.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/bootstrap-extended.css')); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/icons.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/dataTables.bootstrap5.min.css')); ?>">


    <!-- Date Range Picker CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/daterangepicker.css')); ?>">

    <!-- Select 2 css-->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/select2-bootstrap-5-theme.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/select2.min.css')); ?>">

    <!-- Custom css -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/app.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/custom.css')); ?>">

    <!-- Date Range Picker CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    
    

    <link rel="stylesheet" href="<?php echo e(asset('assets/js/sweetalert/sweetalert2.min.css')); ?>">


    <style>
        .btn-xs {
            padding: .25rem .4rem;
            font-size: .75rem;
            line-height: .9;
            border-radius: .2rem;
        }
    </style>

    <style>
        .notification-unread {
            background-color: #f5f5f5;
            /* light gray */
            font-weight: bold;
        }

        .notification-read {
            background-color: white;
            font-weight: normal;
            opacity: 0.85;
        }



        .preloader-image {
            width: 120px;
            height: auto;
            animation: pulse 1.5s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.7;
            }
        }

        /* Differentiate unread vs read */
        .notification-unread {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .notification-read {
            background-color: #fff;
            font-weight: normal;
            color: #6c757d;
        }

        /* Avatar */
        .msg-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }

        /* Text */
        .msg-info {
            font-size: 0.85rem;
            color: #555;
            margin: 0;
        }

        /* Smaller red button */
        .btn-xs {
            padding: 0.15rem 0.4rem;
            font-size: 0.7rem;
            line-height: 1;
            border-radius: 0.2rem;
        }

        /* Preloader Styles */
        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.4s ease;
        }

        .preloader-image {
            width: 120px;
            height: auto;
            animation: pulse 1.5s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.7;
            }
        }

        #offline-popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 10000;
        }

        #offline-popup .popup-content {
            background: #fff;
            padding: 2rem 3rem;
            text-align: center;
            border-radius: 16px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.3s ease-in-out;
        }

        #offline-popup .popup-icon {
            width: 80px;
            height: 80px;
            margin-bottom: 1rem;
            animation: pulse 1.5s infinite;
        }

        #offline-popup h2 {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 0.5rem;
        }

        #offline-popup p {
            color: #555;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
        }
    </style>
    <!-- ✅ Expose Laravel user ID to JS -->
    <script>
        window.Laravel = {
            userId: <?php echo json_encode(auth()->id(), 15, 512) ?>
        };
    </script>

    <?php echo $__env->yieldPushContent('styles'); ?>

    <!-- ✅ Vite compiled assets -->
    
</head>

<body class="font-sans antialiased">
    <!-- Preloader -->
    <div id="preloader">
        <img src="<?php echo e(asset('logo.svg')); ?>" alt="Loading..." class="preloader-image">
        <!-- <div class="loader"></div> -->
    </div>

    <div class="min-h-screen bg-gray-100 wrapper">
        <?php echo $__env->make('layouts.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('layouts.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- Page Heading -->
        <?php if(isset($header)): ?>
            <?php echo e($header); ?>

        <?php endif; ?>

        <!-- Page Content -->
        <main>
            <div class="page-wrapper mt-5 pt-3">
                <div class="page-content footerHeightMob">
                    <?php if (isset($component)) { $__componentOriginal360d002b1b676b6f84d43220f22129e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal360d002b1b676b6f84d43220f22129e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumbs','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumbs'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal360d002b1b676b6f84d43220f22129e2)): ?>
<?php $attributes = $__attributesOriginal360d002b1b676b6f84d43220f22129e2; ?>
<?php unset($__attributesOriginal360d002b1b676b6f84d43220f22129e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal360d002b1b676b6f84d43220f22129e2)): ?>
<?php $component = $__componentOriginal360d002b1b676b6f84d43220f22129e2; ?>
<?php unset($__componentOriginal360d002b1b676b6f84d43220f22129e2); ?>
<?php endif; ?>
                    <?php echo e($slot); ?>

                </div>
            </div>
            <footer class="page-footer">
			<p class="mb-0">Copyright © <?php echo e(date('Y')); ?> Waves | All rights reserved | <?php echo e(config('custom.app_version')); ?> </p>
		</footer>
        </main>
        <!-- Internet Disconnected Popup -->
        <div id="offline-popup">
            <div class="popup-content">
                <img src="<?php echo e(asset('logo.svg')); ?>" alt="No Internet" class="popup-icon">
                <h2>No Internet Connection</h2>
                <p>Please check your network and try again.</p>
            </div>
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="<?php echo e(asset('assets/js/bootstrap.bundle.min.js')); ?>"></script>

    <!-- plugins -->
    <script src="<?php echo e(asset('assets/js/jquery.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/plugins/simplebar/js/simplebar.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/plugins/metismenu/js/metisMenu.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/plugins/chartjs/js/chart.js')); ?>"></script>

    <!-- notification js -->
    <script src="<?php echo e(asset('assets/plugins/notifications/js/lobibox.min.js')); ?>"></script>
     
    <script src="<?php echo e(asset('assets/plugins/notifications/js/notification-custom-script.js')); ?>"></script>
<!-- 
    <script src="<?php echo e(asset('assets/js/dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/dataTables.bootstrap5.min.js')); ?>"></script> -->

    <!-- datatable js -->
    <script src="<?php echo e(asset('assets/plugins/datatable/js/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js')); ?>"></script>

    <!-- Moment JS (external) -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

    <!-- Date Range Picker JS (external) -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">




    <!-- Select 2 js-->
    <script src="<?php echo e(asset('assets/js/select2.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/select2-custom.js')); ?>"></script>


    <!-- app JS -->
    <script src="<?php echo e(asset('assets/js/app.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/ckeditor/ckeditor.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/sweetalert/sweetalert2.all.min.js')); ?>"></script>
    
    


    

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Hide preloader when everything is loaded
            window.onload = function () {
                const preloader = document.getElementById('preloader');
                preloader.style.opacity = '0';
                setTimeout(() => preloader.style.display = 'none', 500);
            };
        });
    </script>

    <script>
        function showOfflinePopup() {
            document.getElementById('offline-popup').style.display = 'flex';
        }

        function hideOfflinePopup() {
            document.getElementById('offline-popup').style.display = 'none';
        }

        // Detect connectivity loss or recovery
        window.addEventListener('offline', showOfflinePopup);
        window.addEventListener('online', hideOfflinePopup);

        // Optional: check on page load
        if (!navigator.onLine) {
            showOfflinePopup();
        }
    </script>



    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html><?php /**PATH /var/www/html/wavestube/resources/views/layouts/app.blade.php ENDPATH**/ ?>