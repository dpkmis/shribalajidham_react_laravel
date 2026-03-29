<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        echo "Seeding permissions...\n";
        
        $count = Permission::seedFromConfig();
        
        echo "✓ Created {$count} permissions\n";
    }
}
