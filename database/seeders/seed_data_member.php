<?php

namespace Database\Seeders;

use App\Models\Data_member;
use Illuminate\Database\Seeder;

class seed_data_member extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Data_member::create([

            'nama_member' => 'Agus',
            'nomor_hp' => '081290020000',
            'alamat' => 'Losarang',
        ]);
    }
}
