<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin account
        Admin::createAdmin([
            'admin_username' => 'admin',
            'admin_password' => 'admin',
            'admin_fname' => 'System',
            'admin_lname' => 'Administrator',
            'admin_cell' => '1234567890',
        ]);

        echo "✅ Default admin created!\n";
        echo "   Username: admin\n";
        echo "   Password: admin\n";

        // You can create more admins here if needed
        // Admin::createAdmin([...]);
    }
}

