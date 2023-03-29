<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         \App\Models\User::factory()->create([
             'name' => 'Artem Admin',
             'email' => 'artem.yablochnyi@gmail.com',
             'password' => Hash::make('admin'),
             'is_admin' => 1
         ]);
    }
}
