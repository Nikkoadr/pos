<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class seed_user extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'email' => 'admin@POS.net',
            'password' => Hash::make('P4ssw0rd'),
            'nama' => 'Administrator',
            'nomor_hp' => '081290020000',
            'role' => 'admin',
        ]);
    }
}
