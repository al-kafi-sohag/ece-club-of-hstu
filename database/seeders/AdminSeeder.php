<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    public function run()
    {
        Admin::firstOrCreate([
            'email' => 'admin@email.com',
        ],[
            'name' => 'Demo Admin',
            'email' => 'admin@email.com',
            'password' => bcrypt('admin1234'),
            'status' => Admin::STATUS_ACTIVE,
        ]);
    }
}
