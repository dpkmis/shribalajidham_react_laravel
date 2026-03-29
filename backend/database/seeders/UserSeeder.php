<?php   

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        echo "Seeding users...\n";
        
        // Create Super Admin User
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@hms.com'],
            [
                'name' => 'Super Admin',
                'password' => 'password', // Will be hashed automatically
                'property_id' => null,
                'is_active' => true,
                'email_verified' => true,
                'email_verified_at' => now()
            ]
        );
        
        $superAdminRole = Role::where('slug', 'super-admin')->first();
        $superAdmin->roles()->sync([$superAdminRole->id]);
        
        echo "✓ Created Super Admin user (admin@hms.com / password)\n";
        
        // Create Property Admin User
        $propertyAdmin = User::firstOrCreate(
            ['email' => 'property@hms.com'],
            [
                'name' => 'Property Admin',
                'password' => 'password',
                'property_id' => 1,
                'is_active' => true,
                'email_verified' => true,
                'email_verified_at' => now()
            ]
        );
        
        $propertyAdminRole = Role::where('slug', 'property-admin')->first();
        $propertyAdmin->roles()->sync([$propertyAdminRole->id]);
        
        echo "✓ Created Property Admin user (property@hms.com / password)\n";
        
        // Create Receptionist User
        $receptionist = User::firstOrCreate(
            ['email' => 'receptionist@hms.com'],
            [
                'name' => 'Receptionist',
                'password' => 'password',
                'property_id' => 1,
                'designation' => 'Front Desk Officer',
                'department' => 'Reception',
                'is_active' => true,
                'email_verified' => true,
                'email_verified_at' => now()
            ]
        );
        
        $receptionistRole = Role::where('slug', 'receptionist')->first();
        $receptionist->roles()->sync([$receptionistRole->id]);
        
        echo "✓ Created Receptionist user (receptionist@hms.com / password)\n";
        
        echo "\n✓ All users seeded successfully!\n";
    }
}