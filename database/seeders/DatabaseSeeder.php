<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@qcms.com'],
            [
                'name' => 'QC Inspector',
                'role' => 'admin',
                'password' => bcrypt('admin123'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'qc@sscworks.com'],
            [
                'name' => 'QC Leader',
                'role' => 'user',
                'password' => bcrypt('admin123'),
            ]
        );
    }
}
