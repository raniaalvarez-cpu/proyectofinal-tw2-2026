<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $tipoAdmin = DB::table('tipos')->where('tipo', 'admin')->first();
        
        DB::table('users')->insert([
            'tipos_id' => $tipoAdmin->id,
            'username' => 'admin',
            'email' => 'admin@sistema.com',
            'password' => Hash::make('admin123'),
            'remember_token' => null,
        ]);
    }
}