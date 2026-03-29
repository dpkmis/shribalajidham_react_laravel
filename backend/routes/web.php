<?php

require __DIR__.'/auth.php';

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\RoomFeatureController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InventoryTransactionController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\SiteMetadataController;






Route::get('/', function () {
    return redirect()->route('login');
});


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route::prefix('roles')->group(function () {
    //     Route::get('/', [RoleController::class, 'index'])->name('roles.index')->middleware('canAccess:roles.index');
    //     Route::get('/{id}', [RoleController::class, 'show'])->name('roles.show');
    //     Route::get('/create', [RoleController::class, 'create'])->name('roles.create')->middleware('canAccess:roles.create');
    //     Route::post('/store', [RoleController::class, 'store'])->name('roles.store');        
    //     Route::put('/update/{id}', [RoleController::class, 'update'])->name('roles.update')->middleware('canAccess:roles.update');
    //     Route::delete('/delete/{id}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('canAccess:roles.delete');
    //     Route::get('roles/ajax', [RoleController::class, 'ajaxRoles'])->name('roles.ajax');
    // });

    // Route::get('/permissions', [PermissionController::class,'index'])->name('permissions.index');
    // Route::get('/permissions/ajax', [PermissionController::class,'ajax'])->name('permissions.ajax');
    // Route::post('/permissions', [PermissionController::class,'store'])->name('permissions.store');
    // Route::get('/permissions/{id}', [PermissionController::class,'show'])->name('permissions.show');
    // Route::put('/permissions/{id}', [PermissionController::class,'update'])->name('permissions.update');
    // Route::delete('/permissions/{id}', [PermissionController::class,'destroy'])->name('permissions.destroy');

    // Room Types
    Route::prefix('room-types')->name('room-types.')
        ->middleware('permission:roomtypes.view')
        ->group(function () {
        Route::get('/', [RoomTypeController::class, 'index'])->name('index');
        Route::get('/ajax', [RoomTypeController::class, 'ajaxRoomTypes'])->name('ajax');
        Route::post('/', [RoomTypeController::class, 'store'])->name('store');
        Route::get('/{id}', [RoomTypeController::class, 'show'])->name('show');
        Route::put('/{id}', [RoomTypeController::class, 'update'])->name('update');
        Route::delete('/{id}', [RoomTypeController::class, 'destroy'])->name('destroy');
        Route::post('/sort-order', [RoomTypeController::class, 'updateSortOrder'])->name('sort-order');
    });

    // Room Features
    Route::prefix('room-features')->name('room-features.')
        ->middleware('permission:roomfeatures.view')
        ->group(function () {
            Route::get('/', [RoomFeatureController::class, 'index'])->name('index');
            Route::get('/ajax', [RoomFeatureController::class, 'ajaxRoomFeatures'])->name('ajax');
            Route::post('/', [RoomFeatureController::class, 'store'])->name('store');
            Route::get('/{id}', [RoomFeatureController::class, 'show'])->name('show');
            Route::put('/{id}', [RoomFeatureController::class, 'update'])->name('update');
            Route::delete('/{id}', [RoomFeatureController::class, 'destroy'])->name('destroy');
    });

    // Rooms
    Route::prefix('rooms')->name('rooms.')
        ->middleware('permission:rooms.view')
        ->group(function () {
            Route::get('/', [RoomController::class, 'index'])->name('index');
            Route::get('/ajax', [RoomController::class, 'ajaxRooms'])->name('ajax');
            Route::post('/', [RoomController::class, 'store'])->name('store');
            Route::get('/{id}', [RoomController::class, 'show'])->name('show');
            Route::put('/{id}', [RoomController::class, 'update'])->name('update');
            Route::delete('/{id}', [RoomController::class, 'destroy'])->name('destroy');
        
        // Additional room endpoints
            Route::post('/bulk-status', [RoomController::class, 'bulkUpdateStatus'])->name('bulk-status');
            Route::get('/available/list', [RoomController::class, 'getAvailableRooms'])->name('available');
    });

     // Guest Management Routes
    
    // Guest Management Routes
    Route::prefix('guests')->name('guests.')
        ->middleware('permission:guests.view')    
        ->group(function () {
        Route::get('/', [GuestController::class, 'index'])->name('index');
        Route::get('/ajax', [GuestController::class, 'ajaxGuests'])->name('ajax');
        Route::post('/', [GuestController::class, 'store'])->name('store');
        Route::get('/{id}', [GuestController::class, 'show'])->name('show');
        Route::put('/{id}', [GuestController::class, 'update'])->name('update');
        Route::delete('/{id}', [GuestController::class, 'destroy'])->name('destroy');
        
        // Additional Guest Actions
        Route::post('/{id}/blacklist', [GuestController::class, 'blacklist'])->name('blacklist');
        Route::post('/{id}/whitelist', [GuestController::class, 'whitelist'])->name('whitelist');
        Route::get('/search/autocomplete', [GuestController::class, 'search'])->name('search');
    });

    // Booking Management Routes
    Route::prefix('bookings')->name('bookings.')
        ->middleware('permission:bookings.view')
        ->group(function () {
            Route::get('/', [BookingController::class, 'index'])->name('index');
            Route::get('/ajax', [BookingController::class, 'ajaxBookings'])->name('ajax');
            Route::post('/', [BookingController::class, 'store'])->name('store');
            Route::get('/{id}', [BookingController::class, 'show'])->name('show');
            Route::put('/{id}', [BookingController::class, 'update'])->name('update');
            Route::delete('/{id}', [BookingController::class, 'destroy'])->name('destroy');
        
            // Booking Actions
            Route::post('/{id}/checkin', [BookingController::class, 'checkIn'])->name('checkin');
            Route::post('/{id}/checkout', [BookingController::class, 'checkOut'])->name('checkout');
            Route::post('/{id}/cancel', [BookingController::class, 'cancel'])->name('cancel');
            Route::post('/{id}/no-show', [BookingController::class, 'markNoShow'])->name('no-show');
            
            // Booking Charges
            Route::post('/{id}/charges', [BookingController::class, 'addCharge'])->name('add-charge');
            Route::delete('/charges/{chargeId}', [BookingController::class, 'deleteCharge'])->name('delete-charge');
            
            // Booking Payments
            Route::post('/{id}/payments', [BookingController::class, 'addPayment'])->name('add-payment');
            Route::get('/{id}/invoice', [BookingController::class, 'generateInvoice'])->name('invoice');
            Route::get('/{id}/receipt/{paymentId}', [BookingController::class, 'generateReceipt'])->name('receipt');
            
            // Booking Statistics
            Route::get('/stats/dashboard', [BookingController::class, 'getStats'])->name('stats');
            
            // Booking Calendar
            Route::get('/calendar/view', [BookingController::class, 'calendarView'])->name('calendar');
            Route::get('/calendar/data', [BookingController::class, 'calendarData'])->name('calendar-data');
            
            // Room Availability
            Route::post('/check-availability', [BookingController::class, 'checkAvailability'])->name('check-availability');
    }); 

    // ============================================
    // FINANCIAL ROUTES - INVOICES
    // ============================================
    Route::prefix('financials/invoices')->name('financials.invoices.')->group(function () {
        // Main routes
        Route::get('/', [FinancialController::class, 'invoiceIndex'])->name('index');
        Route::get('/ajax', [FinancialController::class, 'invoiceAjax'])->name('ajax');
        Route::post('/store', [FinancialController::class, 'invoiceStore'])->name('store');
        Route::get('/{id}', [FinancialController::class, 'invoiceShow'])->name('show');
        Route::put('/{id}', [FinancialController::class, 'invoiceUpdate'])->name('update');
        Route::delete('/{id}', [FinancialController::class, 'invoiceDestroy'])->name('destroy');
        
        // Invoice Actions
        Route::post('/{id}/cancel', [FinancialController::class, 'invoiceCancel'])->name('cancel');
        Route::post('/{id}/send-email', [FinancialController::class, 'invoiceSendEmail'])->name('send-email');
        Route::get('/{id}/pdf', [FinancialController::class, 'invoicePDF'])->name('pdf');
        
        // Generate from booking
        Route::post('/generate-from-booking/{bookingId}', [FinancialController::class, 'generateFromBooking'])->name('generate-from-booking');
    });
    
    // ============================================
    // FINANCIAL ROUTES - PAYMENTS
    // ============================================
    Route::prefix('financials/payments')->name('financials.payments.')->group(function () {
        // Main routes
        Route::get('/', [FinancialController::class, 'paymentIndex'])->name('index');
        Route::get('/ajax', [FinancialController::class, 'paymentAjax'])->name('ajax');
        Route::post('/store', [FinancialController::class, 'paymentStore'])->name('store');
        Route::get('/{id}', [FinancialController::class, 'paymentShow'])->name('show');
        Route::delete('/{id}', [FinancialController::class, 'paymentDestroy'])->name('destroy');
        
        // Payment Actions
        Route::post('/{id}/allocate', [FinancialController::class, 'allocatePayment'])->name('allocate');
        Route::get('/{id}/receipt', [FinancialController::class, 'paymentReceipt'])->name('receipt');
        Route::post('/{id}/send-receipt', [FinancialController::class, 'paymentSendReceipt'])->name('send-receipt');
        Route::post('/{id}/void', [FinancialController::class, 'voidPayment'])->name('void');
        
        // Cheque specific
        Route::post('/{id}/mark-cleared', [FinancialController::class, 'markChequeCleared'])->name('mark-cleared');
    });
    
    // ============================================
    // FINANCIAL ROUTES - REFUNDS
    // ============================================
    Route::prefix('financials/refunds')->name('financials.refunds.')->group(function () {
        // Main routes
        Route::get('/', [FinancialController::class, 'refundIndex'])->name('index');
        Route::get('/ajax', [FinancialController::class, 'refundAjax'])->name('ajax');
        Route::get('/{id}', [FinancialController::class, 'refundShow'])->name('show');
        
        // Refund Actions
        Route::post('/initiate/{paymentId}', [FinancialController::class, 'initiateRefund'])->name('initiate');
        Route::post('/{id}/approve', [FinancialController::class, 'approveRefund'])->name('approve');
        Route::post('/{id}/process', [FinancialController::class, 'processRefund'])->name('process');
        Route::post('/{id}/complete', [FinancialController::class, 'completeRefund'])->name('complete');
        Route::post('/{id}/cancel', [FinancialController::class, 'cancelRefund'])->name('cancel');
        Route::post('/{id}/fail', [FinancialController::class, 'failRefund'])->name('fail');
    });
    
    // ============================================
    // FINANCIAL ROUTES - REPORTS & ANALYTICS
    // ============================================
    Route::prefix('financials/reports')->name('financials.reports.')->group(function () {
        Route::get('/dashboard', [FinancialController::class, 'reportsDashboard'])->name('dashboard');
        Route::get('/stats', [FinancialController::class, 'getFinancialStats'])->name('stats');
        Route::get('/aging-report', [FinancialController::class, 'agingReport'])->name('aging');
        Route::get('/payment-summary', [FinancialController::class, 'paymentSummary'])->name('payment-summary');
        Route::get('/revenue-analysis', [FinancialController::class, 'revenueAnalysis'])->name('revenue-analysis');
        Route::get('/tax-summary', [FinancialController::class, 'taxSummary'])->name('tax-summary');
    });
    
    
    Route::prefix('users')->name('users.')
        ->middleware('permission:users.view')
        ->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/ajax', [UserController::class, 'ajaxUsers'])->name('ajax');
            Route::get('/stats', [UserController::class, 'getUserStats'])->name('stats');
            Route::get('/{id}', [UserController::class, 'show'])->name('show');            
            Route::post('/store', [UserController::class, 'store'])->name('store')->middleware('permission:users.create');                
            Route::put('/{id}', [UserController::class, 'update'])->name('update')->middleware('permission:users.edit');            
            Route::post('/{id}/unlock', [UserController::class, 'unlock'])->name('unlock')->middleware('permission:users.edit');
            Route::get('/{id}/status', [UserController::class, 'getStatus'])->name('status');
            Route::post('/{id}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password')->middleware('permission:users.edit');
            Route::get('/{id}/activity', [UserController::class, 'getActivity'])->name('activity');
            Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy')->middleware('permission:users.delete');
            // ... other routes
        });

         // Roles Management
    Route::prefix('roles')->name('roles.')->middleware('permission:roles.view')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/ajax', [RoleController::class, 'ajaxRoles'])->name('ajax');
        Route::post('/store', [RoleController::class, 'store'])
            ->name('store')
            ->middleware('permission:roles.create');
        Route::get('/{id}', [RoleController::class, 'show'])->name('show');
        Route::put('/{id}', [RoleController::class, 'update'])
            ->name('update')
            ->middleware('permission:roles.edit');
            Route::delete('/{id}', [RoleController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:roles.delete');
    });

    Route::prefix('housekeeping')->middleware(['auth'])->group(function () {
        // Tasks
        Route::get('/', [App\Http\Controllers\HousekeepingController::class, 'index'])->name('housekeeping.index');
        Route::get('/ajax', [App\Http\Controllers\HousekeepingController::class, 'ajaxTasks'])->name('housekeeping.ajax');
        Route::post('/', [App\Http\Controllers\HousekeepingController::class, 'store'])->name('housekeeping.store');
        Route::get('/{id}', [App\Http\Controllers\HousekeepingController::class, 'show'])->name('housekeeping.show');
        Route::put('/{id}', [App\Http\Controllers\HousekeepingController::class, 'update'])->name('housekeeping.update');
        Route::delete('/{id}', [App\Http\Controllers\HousekeepingController::class, 'destroy'])->name('housekeeping.destroy');
        
        // Task Operations
        Route::post('/start-task', [App\Http\Controllers\HousekeepingController::class, 'startTask'])->name('housekeeping.start-task');
        Route::post('/complete-task', [App\Http\Controllers\HousekeepingController::class, 'completeTask'])->name('housekeeping.complete-task');
        Route::post('/inspect-task', [App\Http\Controllers\HousekeepingController::class, 'inspectTask'])->name('housekeeping.inspect-task');
        
        // Helper Routes
        Route::get('/rooms-by-property', [App\Http\Controllers\HousekeepingController::class, 'getRoomsByProperty'])->name('housekeeping.rooms-by-property');
        Route::get('/dashboard-stats', [App\Http\Controllers\HousekeepingController::class, 'getDashboardStats'])->name('housekeeping.dashboard-stats');
    });

    // Housekeeping Staff Routes
    Route::prefix('housekeeping-staff')->middleware(['auth'])->group(function () {
        // Basic CRUD
        Route::get('/', [App\Http\Controllers\HousekeepingStaffController::class, 'index'])->name('housekeeping-staff.index');
        Route::get('/ajax', [App\Http\Controllers\HousekeepingStaffController::class, 'ajaxStaff'])->name('housekeeping-staff.ajax');
        Route::post('/', [App\Http\Controllers\HousekeepingStaffController::class, 'store'])->name('housekeeping-staff.store');
        Route::get('/{id}', [App\Http\Controllers\HousekeepingStaffController::class, 'show'])->name('housekeeping-staff.show');
        Route::put('/{id}', [App\Http\Controllers\HousekeepingStaffController::class, 'update'])->name('housekeeping-staff.update');
        Route::delete('/{id}', [App\Http\Controllers\HousekeepingStaffController::class, 'destroy'])->name('housekeeping-staff.destroy');
        
        // Staff Operations
        Route::post('/toggle-status', [App\Http\Controllers\HousekeepingStaffController::class, 'toggleStatus'])->name('housekeeping-staff.toggle-status');
        Route::post('/mark-attendance', [App\Http\Controllers\HousekeepingStaffController::class, 'markAttendance'])->name('housekeeping-staff.mark-attendance');
        Route::post('/bulk-assign-tasks', [App\Http\Controllers\HousekeepingStaffController::class, 'bulkAssignTasks'])->name('housekeeping-staff.bulk-assign-tasks');
        
        // Reports & Analytics
        Route::get('/{id}/workload', [App\Http\Controllers\HousekeepingStaffController::class, 'getWorkload'])->name('housekeeping-staff.workload');
        Route::get('/{id}/performance', [App\Http\Controllers\HousekeepingStaffController::class, 'getPerformance'])->name('housekeeping-staff.performance');
        Route::get('/available-staff', [App\Http\Controllers\HousekeepingStaffController::class, 'getAvailableStaff'])->name('housekeeping-staff.available-staff');
    });

    Route::prefix('inventory')->middleware(['auth'])->group(function () {
        // Tasks
        Route::get('/', [App\Http\Controllers\InventoryController::class, 'index'])->name('inventory.index');
        Route::get('/ajax', [App\Http\Controllers\InventoryController::class, 'ajaxInventory'])->name('inventory.ajax');
        Route::post('/', [App\Http\Controllers\InventoryController::class, 'store'])->name('inventory.store');
        Route::get('/{id}', [App\Http\Controllers\InventoryController::class, 'show'])->name('inventory.show');
        Route::put('/{id}', [App\Http\Controllers\InventoryController::class, 'update'])->name('inventory.update');
        Route::delete('/{id}', [App\Http\Controllers\InventoryController::class, 'destroy'])->name('inventory.destroy');
        
        // // Task Operations
        // Route::post('/start-task', [App\Http\Controllers\InventoryController::class, 'startTask'])->name('inventory.start-task');
        // Route::post('/complete-task', [App\Http\Controllers\InventoryController::class, 'completeTask'])->name('inventory.complete-task');
        // Route::post('/inspect-task', [App\Http\Controllers\InventoryController::class, 'inspectTask'])->name('inventory.inspect-task');
        
        // // Helper Routes
        // Route::get('/rooms-by-property', [App\Http\Controllers\InventoryController::class, 'getRoomsByProperty'])->name('inventory.rooms-by-property');
        // Route::get('/dashboard-stats', [App\Http\Controllers\InventoryController::class, 'getDashboardStats'])->name('inventory.dashboard-stats');
    });

    Route::prefix('inventory')->name('inventory.')->group(function () {

        // Inventory CRUD
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/ajax', [InventoryController::class, 'ajax'])->name('ajax');
        Route::post('/', [InventoryController::class, 'store'])->name('store');
        Route::get('/{id}', [InventoryController::class, 'show'])->name('show');
        Route::put('/{id}', [InventoryController::class, 'update'])->name('update');
        Route::delete('/{id}', [InventoryController::class, 'destroy'])->name('destroy');

        // Stock Operations
        Route::post('/stock-in', [InventoryController::class, 'stockIn'])
            ->name('stock-in');

        Route::post('/stock-out', [InventoryTransactionController::class, 'stockOut'])
            ->name('stock-out');

        Route::post('/adjust-stock', [InventoryTransactionController::class, 'adjustStock'])
            ->name('adjust-stock');

        // // Inventory History
        Route::get('/{id}/history', [InventoryTransactionController::class, 'history'])
            ->name('history');

        // Reports
        
            Route::get('/low-stock', [InventoryController::class, 'lowStock'])
                ->name('low-stock');

            Route::get('/expiring', [InventoryController::class, 'expiring'])
                ->name('expiring');
        

    });

    // ── Website Master Data ──────────────────────────────────────
    Route::prefix('master')->name('master.')->group(function () {

        // Tour Packages
        Route::prefix('tour-packages')->name('tour-packages.')->group(function () {
            Route::get('/', [MasterDataController::class, 'tourPackages'])->name('index');
            Route::get('/ajax', [MasterDataController::class, 'tourPackagesAjax'])->name('ajax');
            Route::post('/', [MasterDataController::class, 'tourPackageStore'])->name('store');
            Route::get('/{id}', [MasterDataController::class, 'tourPackageShow'])->name('show');
            Route::put('/{id}', [MasterDataController::class, 'tourPackageUpdate'])->name('update');
            Route::delete('/{id}', [MasterDataController::class, 'tourPackageDestroy'])->name('destroy');
        });

        // Festival Offers
        Route::prefix('festival-offers')->name('festival-offers.')->group(function () {
            Route::get('/', [MasterDataController::class, 'festivalOffers'])->name('index');
            Route::get('/ajax', [MasterDataController::class, 'festivalOffersAjax'])->name('ajax');
            Route::post('/', [MasterDataController::class, 'festivalOfferStore'])->name('store');
            Route::get('/{id}', [MasterDataController::class, 'festivalOfferShow'])->name('show');
            Route::put('/{id}', [MasterDataController::class, 'festivalOfferUpdate'])->name('update');
            Route::delete('/{id}', [MasterDataController::class, 'festivalOfferDestroy'])->name('destroy');
        });

        // Testimonials
        Route::prefix('testimonials')->name('testimonials.')->group(function () {
            Route::get('/', [MasterDataController::class, 'testimonials'])->name('index');
            Route::get('/ajax', [MasterDataController::class, 'testimonialsAjax'])->name('ajax');
            Route::post('/', [MasterDataController::class, 'testimonialStore'])->name('store');
            Route::get('/{id}', [MasterDataController::class, 'testimonialShow'])->name('show');
            Route::put('/{id}', [MasterDataController::class, 'testimonialUpdate'])->name('update');
            Route::delete('/{id}', [MasterDataController::class, 'testimonialDestroy'])->name('destroy');
        });

        // Blog Posts
        Route::prefix('blog-posts')->name('blog-posts.')->group(function () {
            Route::get('/', [MasterDataController::class, 'blogPosts'])->name('index');
            Route::get('/ajax', [MasterDataController::class, 'blogPostsAjax'])->name('ajax');
            Route::post('/', [MasterDataController::class, 'blogPostStore'])->name('store');
            Route::get('/{id}', [MasterDataController::class, 'blogPostShow'])->name('show');
            Route::put('/{id}', [MasterDataController::class, 'blogPostUpdate'])->name('update');
            Route::delete('/{id}', [MasterDataController::class, 'blogPostDestroy'])->name('destroy');
        });

        // Gallery
        Route::prefix('gallery')->name('gallery.')->group(function () {
            Route::get('/', [MasterDataController::class, 'gallery'])->name('index');
            Route::get('/ajax', [MasterDataController::class, 'galleryAjax'])->name('ajax');
            Route::post('/', [MasterDataController::class, 'galleryStore'])->name('store');
            Route::get('/{id}', [MasterDataController::class, 'galleryShow'])->name('show');
            Route::put('/{id}', [MasterDataController::class, 'galleryUpdate'])->name('update');
            Route::delete('/{id}', [MasterDataController::class, 'galleryDestroy'])->name('destroy');
        });

        // Site Metadata
        Route::prefix('metadata')->name('metadata.')->group(function () {
            Route::get('/', [SiteMetadataController::class, 'index'])->name('index');
            Route::post('/update', [SiteMetadataController::class, 'update'])->name('update');
            Route::post('/add-field', [SiteMetadataController::class, 'addField'])->name('add-field');
            Route::delete('/field/{id}', [SiteMetadataController::class, 'deleteField'])->name('delete-field');
        });

        // Nearby Attractions
        Route::prefix('nearby-attractions')->name('nearby-attractions.')->group(function () {
            Route::get('/', [MasterDataController::class, 'nearbyAttractions'])->name('index');
            Route::get('/ajax', [MasterDataController::class, 'nearbyAttractionsAjax'])->name('ajax');
            Route::post('/', [MasterDataController::class, 'nearbyAttractionStore'])->name('store');
            Route::get('/{id}', [MasterDataController::class, 'nearbyAttractionShow'])->name('show');
            Route::put('/{id}', [MasterDataController::class, 'nearbyAttractionUpdate'])->name('update');
            Route::delete('/{id}', [MasterDataController::class, 'nearbyAttractionDestroy'])->name('destroy');
        });
    });


});

