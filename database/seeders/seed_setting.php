<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class seed_setting extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::create([
            'nama_toko' => 'Angel Cell',
            'alamat_toko' => 'Jangga',
            'nama_perinter' => 'bener',
        ]);
    }
}
