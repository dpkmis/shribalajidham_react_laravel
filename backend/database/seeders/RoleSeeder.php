<?php


namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        echo "Seeding roles...\n";
        
        // 1. Super Admin (Global - All Permissions)
        $superAdmin = Role::firstOrCreate(
            ['slug' => 'super-admin'],
            [
                'name' => 'Super Admin',
                'property_id' => null,
                'description' => 'Full system access across all properties'
            ]
        );
        $superAdmin->permissions()->sync(Permission::all()->pluck('id'));
        echo "✓ Created Super Admin role with all permissions\n";
        
        // 2. Property Admin (Property-specific - All Permissions)
        $propertyAdmin = Role::firstOrCreate(
            ['slug' => 'property-admin'],
            [
                'name' => 'Property Admin',
                'property_id' => 1, // You can change this
                'description' => 'Full access to property management'
            ]
        );
        $propertyAdmin->permissions()->sync(Permission::all()->pluck('id'));
        echo "✓ Created Property Admin role\n";
        
        // 3. Manager
        $manager = Role::firstOrCreate(
            ['slug' => 'manager'],
            [
                'name' => 'Manager',
                'property_id' => 1,
                'description' => 'Manage bookings, guests, and operations'
            ]
        );
        $managerPermissions = Permission::whereIn('slug', [
            'dashboard.view',
            'bookings.view', 'bookings.create', 'bookings.edit', 'bookings.checkin', 'bookings.checkout',
            'guests.view', 'guests.create', 'guests.edit',
            'rooms.view', 'room-types.view',
            'invoices.view', 'payments.view',
            'reports.view', 'reports.export'
        ])->pluck('id');
        $manager->permissions()->sync($managerPermissions);
        echo "✓ Created Manager role\n";
        
        // 4. Receptionist
        $receptionist = Role::firstOrCreate(
            ['slug' => 'receptionist'],
            [
                'name' => 'Receptionist',
                'property_id' => 1,
                'description' => 'Handle check-ins, check-outs, and guest management'
            ]
        );
        $receptionistPermissions = Permission::whereIn('slug', [
            'dashboard.view',
            'bookings.view', 'bookings.create', 'bookings.edit', 'bookings.checkin', 'bookings.checkout',
            'guests.view', 'guests.create', 'guests.edit',
            'rooms.view', 'room-types.view'
        ])->pluck('id');
        $receptionist->permissions()->sync($receptionistPermissions);
        echo "✓ Created Receptionist role\n";
        
        // 5. Accountant
        $accountant = Role::firstOrCreate(
            ['slug' => 'accountant'],
            [
                'name' => 'Accountant',
                'property_id' => 1,
                'description' => 'Manage financial transactions and reports'
            ]
        );
        $accountantPermissions = Permission::whereIn('slug', [
            'dashboard.view',
            'bookings.view',
            'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.send',
            'payments.view', 'payments.create', 'payments.edit',
            'refunds.view', 'refunds.create',
            'reports.view', 'reports.export'
        ])->pluck('id');
        $accountant->permissions()->sync($accountantPermissions);
        echo "✓ Created Accountant role\n";
        
        // 6. Housekeeping
        $housekeeping = Role::firstOrCreate(
            ['slug' => 'housekeeping'],
            [
                'name' => 'Housekeeping',
                'property_id' => 1,
                'description' => 'View bookings and room status'
            ]
        );
        $housekeepingPermissions = Permission::whereIn('slug', [
            'dashboard.view',
            'bookings.view',
            'rooms.view'
        ])->pluck('id');
        $housekeeping->permissions()->sync($housekeepingPermissions);
        echo "✓ Created Housekeeping role\n";
        
        echo "\n✓ All roles seeded successfully!\n";
    }
}
