<?php

namespace Database\Seeders;

use App\Models\Data_barang;
use Illuminate\Database\Seeder;

class seed_data_barang extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Data_barang::create([
            'id_toko' => '1',
            'id_supplier' => '1',
            'nama' => 'Casing',
            'qty' => '100',
            'harga_modal' => '1000',
            'harga_jual1' => '2000',
            'harga_jual2' => '3000',

        ]);
    }
}
