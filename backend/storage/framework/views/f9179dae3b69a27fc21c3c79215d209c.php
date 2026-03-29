<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div class="d-flex align-items-center gap-2">
            <img src="<?php echo e(asset('assets/images/my_waves.png')); ?>" class="logo-icon-2" alt="logo">
            <img src="<?php echo e(asset('assets/images/my_waves_text.png')); ?>" class="logo-text" alt="logo">
        </div>
        <div class="toggle-icon ms-auto">
            <i class='bx bx-arrow-back'></i>
        </div>
    </div>

    <ul class="metismenu" id="menu">

        
        <li>
            <a href="<?php echo e(route('dashboard')); ?>">
                <div class="parent-icon">
                    <i class="bx bx-home-alt"></i>
                </div>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>

        
        <li>
            <a class="has-arrow" href="javascript:void(0);">
                <div class="parent-icon">
                    <i class="bx bx-globe"></i>
                </div>
                <span class="menu-title">Website Master</span>
            </a>
            <ul>
                <li><a href="<?php echo e(route('master.metadata.index')); ?>"><i class='bx bx-cog'></i>Site Metadata</a></li>
                <li><a href="<?php echo e(route('master.tour-packages.index')); ?>"><i class='bx bx-map-alt'></i>Tour Packages</a></li>
                <li><a href="<?php echo e(route('master.festival-offers.index')); ?>"><i class='bx bx-gift'></i>Festival Offers</a></li>
                <li><a href="<?php echo e(route('master.testimonials.index')); ?>"><i class='bx bx-star'></i>Testimonials</a></li>
                <li><a href="<?php echo e(route('master.blog-posts.index')); ?>"><i class='bx bx-edit'></i>Blog Posts</a></li>
                <li><a href="<?php echo e(route('master.gallery.index')); ?>"><i class='bx bx-image'></i>Gallery</a></li>
                <li><a href="<?php echo e(route('master.nearby-attractions.index')); ?>"><i class='bx bx-map-pin'></i>Nearby Attractions</a></li>
            </ul>
        </li>

        
        
        

        
        <li>
            <a class="has-arrow" href="javascript:void(0);">
                <div class="parent-icon">
                    <i class="bx bx-shield-quarter"></i>
                </div>
                <span class="menu-title">Admin Management</span>
            </a>
            <ul>
                <li>
                    <a href="<?php echo e(route('users.index')); ?>">
                        <i class='bx bx-user-check'></i>User Management
                    </a>
                </li>
                <li>
                    <a href="<?php echo e(route('roles.index')); ?>">
                        <i class='bx bx-user-check'></i>Role Management
                    </a>
                </li>
            </ul>
        </li>

        
        <li>
            <a class="has-arrow" href="javascript:void(0);">
                <div class="parent-icon">
                    <i class="bx bx-building-house"></i>
                </div>
                <span class="menu-title">Room Management</span>
            </a>
            <ul>
                <li>
                    <a href="<?php echo e(route('room-features.index')); ?>">
                        <i class='bx bx-layer'></i>Room Features
                    </a>
                </li>
                <li>
                    <a href="<?php echo e(route('room-types.index')); ?>">
                        <i class='bx bx-category-alt'></i>Room Types
                    </a>
                </li>
                <li>
                    <a href="<?php echo e(route('rooms.index')); ?>">
                        <i class='bx bx-door-open'></i>Rooms
                    </a>
                </li>
            </ul>
        </li>

        
        <li>
            <a class="has-arrow" href="javascript:void(0);">
                <div class="parent-icon">
                    <i class="bx bx-calendar-check"></i>
                </div>
                <span class="menu-title">Booking Management</span>
            </a>
            <ul>
                <li>
                    <a href="<?php echo e(route('guests.index')); ?>">
                        <i class='bx bx-user'></i>Guests
                    </a>
                </li>
                <li>
                    <a href="<?php echo e(route('bookings.index')); ?>">
                        <i class='bx bx-book'></i>Bookings
                    </a>
                </li>
            </ul>
        </li>

        <li>
            <a class="has-arrow" href="javascript:void(0);">
                <div class="parent-icon">
                    <i class="bx bx-home-heart"></i>
                </div>
                <span class="menu-title">Housekeeping</span>
            </a>
            <ul>                
                <li>
                    <a href="<?php echo e(route('housekeeping.index')); ?>">
                         <i class="bx bx-task"></i>
                        <span>Tasks</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo e(route('housekeeping-staff.index')); ?>">
                        <i class="bx bx-group"></i>
                        <span>Staff</span>
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a class="has-arrow" href="javascript:void(0);">
                <div class="parent-icon">
                    <i class="bx bx-home-heart"></i>
                </div>
                <span class="menu-title">Inventory Management</span>
            </a>
            <ul>                
                <li>
                    <a href="<?php echo e(route('inventory.index')); ?>">
                         <i class="bx bx-task"></i>
                        <span>Inventory</span>
                    </a>
                </li>                
            </ul>
        </li>
    </ul>
</div><?php /**PATH /var/www/html/shri_balaji_dham/backend/resources/views/layouts/sidebar.blade.php ENDPATH**/ ?>