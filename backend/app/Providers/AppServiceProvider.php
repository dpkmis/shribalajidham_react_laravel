<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
// use App\Services\PermissionService;
use Illuminate\Support\Facades\URL;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot()
    {
        // Permission directives
        Blade::if('hasPermission', function ($permission) {
            return auth()->check() && auth()->user()->hasPermission($permission);
        });

        Blade::if('hasAnyPermission', function (...$permissions) {
            return auth()->check() && auth()->user()->hasAnyPermission($permissions);
        });

        Blade::if('hasAllPermissions', function (...$permissions) {
            return auth()->check() && auth()->user()->hasAllPermissions($permissions);
        });

        // Role directives
        Blade::if('hasRole', function ($role) {
            return auth()->check() && auth()->user()->hasRole($role);
        });

        Blade::if('hasAnyRole', function (...$roles) {
            return auth()->check() && auth()->user()->hasAnyRole($roles);
        });

        // Module directive
        Blade::if('canAccessModule', function ($module) {
            return auth()->check() && auth()->user()->canAccessModule($module);
        });

        // Super admin check
        Blade::if('isSuperAdmin', function () {
            return auth()->check() && auth()->user()->isSuperAdmin();
        });

        Blade::if('isPropertyAdmin', function () {
            return auth()->check() && auth()->user()->isPropertyAdmin();
        });
    }
    
}
