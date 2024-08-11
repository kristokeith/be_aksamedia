<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\Division;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Admin::create([
            'id' => Str::uuid(),
            'name' => 'Admin Aksamedia',
            'username' => 'admin_aksa',
            'password' => Hash::make('password'),
            'phone' => '081234567890',
            'email' => 'admin@aksamedia.com',
        ]);
    }
}
