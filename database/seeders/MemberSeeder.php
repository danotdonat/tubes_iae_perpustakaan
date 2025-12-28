<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        Member::create(['name' => 'Irfan Fathoni', 'email' => 'irfan@example.com', 'phone' => '08123456789']);
        Member::create(['name' => 'Rama Galih', 'email' => 'rama@example.com', 'phone' => '08987654321']);
        Member::create(['name' => 'Haqi', 'email' => 'Haqi@example.com', 'phone' => '08987654322']);
    }
}
