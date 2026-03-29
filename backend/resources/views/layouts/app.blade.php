<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'WavesTube') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- favicon -->
    <link rel="icon" href="{{ asset('fabIcon.svg') }}" type="image/png" />

    <!-- plugins -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/notifications/css/lobibox.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/plugins/simplebar/css/simplebar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/plugins/metismenu/css/metisMenu.min.css') }}" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-extended.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/dataTables.bootstrap5.min.css') }}">


    <!-- Date Range Picker CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/daterangepicker.css') }}">

    <!-- Select 2 css-->
    <link rel="stylesheet" href="{{ asset('assets/css/select2-bootstrap-5-theme.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">

    <!-- Custom css -->
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

    <!-- Date Range Picker CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    
    {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}

    <link rel="stylesheet" href="{{ asset('assets/js/sweetalert/sweetalert2.min.css') }}">


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
            userId: @json(auth()->id())
        };
    </script>

    @stack('styles')

    <!-- ✅ Vite compiled assets -->
    {{-- @vite(['resources/js/app.js']) --}}
</head>

<body class="font-sans antialiased">
    <!-- Preloader -->
    <div id="preloader">
        <img src="{{ asset('logo.svg') }}" alt="Loading..." class="preloader-image">
        <!-- <div class="loader"></div> -->
    </div>

    <div class="min-h-screen bg-gray-100 wrapper">
        @include('layouts.sidebar')
        @include('layouts.navbar')

        <!-- Page Heading -->
        @isset($header)
            {{ $header }}
        @endisset

        <!-- Page Content -->
        <main>
            <div class="page-wrapper mt-5 pt-3">
                <div class="page-content footerHeightMob">
                    <x-breadcrumbs />
                    {{ $slot }}
                </div>
            </div>
            <footer class="page-footer">
			<p class="mb-0">Copyright © {{ date('Y') }} Waves | All rights reserved | {{ config('custom.app_version') }} </p>
		</footer>
        </main>
        <!-- Internet Disconnected Popup -->
        <div id="offline-popup">
            <div class="popup-content">
                <img src="{{ asset('logo.svg') }}" alt="No Internet" class="popup-icon">
                <h2>No Internet Connection</h2>
                <p>Please check your network and try again.</p>
            </div>
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

    <!-- plugins -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/simplebar/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/metismenu/js/metisMenu.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
    <script src="{{ asset('assets/plugins/chartjs/js/chart.js') }}"></script>

    <!-- notification js -->
    <script src="{{ asset('assets/plugins/notifications/js/lobibox.min.js') }}"></script>
     {{-- <script src="{{ asset('assets/plugins/notifications/js/notifications.min.js') }}"></script> --}}
    <script src="{{ asset('assets/plugins/notifications/js/notification-custom-script.js') }}"></script>
<!-- 
    <script src="{{ asset('assets/js/dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/dataTables.bootstrap5.min.js') }}"></script> -->

    <!-- datatable js -->
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>

    <!-- Moment JS (external) -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

    <!-- Date Range Picker JS (external) -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">




    <!-- Select 2 js-->
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2-custom.js') }}"></script>


    <!-- app JS -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert/sweetalert2.all.min.js') }}"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}
    


    {{-- <script>

        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
        @if (session('type'))
            alert_box("{{ strtolower(session('type')) }}", "{{ session('msg') }}");
        @endif

            function fetchNotifications() {
                fetch("notifications")
                    .then(res => res.json())
                    .then(data => {
                        let list = document.getElementById("notificationList");
                        list.innerHTML = "";

                        data.all.forEach(n => {
                            const isUnread = !n.read_at;

                            list.innerHTML += `
                            <a class="dropdown-item ${isUnread ? 'notification-unread' : 'notification-read'}" href="javascript:;">
                                <div class="d-flex align-items-center">
                                    <div class="user-online">
                                        ${n.data.profile_picture ? `<img src="${n.data.profile_picture}" class="msg-avatar" alt="user avatar">` : ""}
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="msg-name dflex">
                                            ${n.data.meta_name}
                                            <span class="msg-time">
                                                ${isUnread ? `<button class="btn btn-xs btn-danger" onclick="markRead('${n.id}')">Mark Read</button>` : ""}
                                            </span>
                                        </h6>
                                        <p class="msg-info">${n.data.message}</p>
                                    </div>
                                </div>
                            </a>`;
                        });

                        // update badge count
                        document.getElementById("unreadCount").innerText = data.unread.length || "";
                    });
            }

        function markRead(id) {
            fetch(`notifications/${id}/read`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    "Content-Type": "application/json"
                }
            }).then(() => {
                // instead of refreshing all, just update the UI of this one
                let btn = document.querySelector(`[onclick="markRead('${id}')"]`);
                if (btn) {
                    let parent = btn.closest(".dropdown-item");
                    parent.classList.remove("notification-unread");
                    parent.classList.add("notification-read");
                    btn.remove(); // remove button
                }

                // decrement badge count
                let unread = document.getElementById("unreadCount");
                let current = parseInt(unread.innerText || 0);
                unread.innerText = current > 0 ? current - 1 : "";
            });
        }


        // Poll every 30 seconds
        fetchNotifications();
        // setInterval(fetchNotifications, 30000);
    </script> --}}

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



    @stack('scripts')
</body>

</html>