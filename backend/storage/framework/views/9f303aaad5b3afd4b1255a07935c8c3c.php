<header>
    <div class="topbar d-flex align-items-center">
        <nav class="navbar navbar-expand gap-3">
            <div class="mobile-toggle-menu"><i class='bx bx-menu'></i>
            </div>

            <div class="search-bar d-none" data-bs-toggle="modal" data-bs-target="#SearchModal">
                <a href="avascript:;" class="btn d-flex align-items-center"><i class='bx bx-search'></i>Search</a>
            </div>
            <div class="top-menu ms-auto">
                <ul class="navbar-nav align-items-center gap-1">
                    <li class="nav-item dark-mode d-none d-sm-flex" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Background Theme">
                        <a class="nav-link dark-mode-icon d-none" href="javascript:;"><i class='bx bx-moon'></i>
                        </a>
                    </li>                 
                    <li class="nav-item dropdown dropdown-large d-none" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Notification">
                        <a id="notificationDropdown" onclick="toggleNotifications()" class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#"
                            data-bs-toggle="dropdown"><span class="alert-count" id="unreadCount"></span>
                            <i class='bx bx-bell'></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:;">
                                <div class="msg-header">
                                    <p class="msg-header-title">Notifications</p>                                   
                                </div>
                            </a>
                            <div id="notificationList" class="header-notifications-list">
                                 <a class="dropdown-item" href="javascript:;">
                                    <div class="d-flex align-items-center">                                       
                                        <div class="flex-grow-1">
                                            <h6 class="msg-name">Loading...</h6>                                            
                                        </div>
                                    </div>
                                    </a>                                
                            </div>
                            <!-- <a href="javascript:;">
                                <div class="text-center msg-footer">
                                    <button class="btn btn-primary w-100">View All Notifications</button>
                                </div>
                            </a> -->
                        </div>
                    </li>
                </ul>
            </div>

             <?php
                $placeholderImage = config('custom.USER_PLACEHOLDER_IMAGE');
                $image = Auth::user()->profile;                                
            ?>

            <div class="user-box dropdown px-3">
                <a class="d-flex align-items-center nav-link dropdown-toggle gap-3 dropdown-toggle-nocaret" href="#"
                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="<?php echo e(!empty($image) ? $image : asset($placeholderImage)); ?>" class="user-img" alt="user avatar">
                    <div class="user-info">
                        <p class="user-name mb-0"><?php echo e(Auth::user()->name); ?></p>
                        <p class="designattion mb-0"><?php echo e(Auth::user()->email); ?></p>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end profileList">
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="<?php echo e(route('profile.edit')); ?>">
                            <i class="bx bx-user fs-5"></i><span>Profile</span></a>
                    </li>
                    <li>
                        <form method="POST" action="<?php echo e(route('logout')); ?>">
                            <?php echo csrf_field(); ?>

                            <a class="dropdown-item d-flex align-items-center" href="route('logout')" onclick="event.preventDefault();
                                            this.closest('form').submit();">
                                <button class="dropdown-item d-flex align-items-center p-0" href="javascript:;"><i
                                        class="bx bx-log-out-circle"></i><span>Logout</span></button>
                                 </a>
                        </form>


                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header><?php /**PATH /var/www/html/shribalajidham_react_laravel/backend/resources/views/layouts/navbar.blade.php ENDPATH**/ ?>