<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Di sini kita panggil semua seeder yang sudah kamu buat
        $this->call([
            UserSeeder::class,
            BookSeeder::class,
            MemberSeeder::class,
        ]);
    }
}
