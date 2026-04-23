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
            'nama' => 'Yakult 1 Botol',
            'qty' => '1000',
            'harga_modal' => '2000',
            'barcode' => '123456789012',
            'harga_umum' => '2500',
            'harga_member' => '9500',

        ]);
    }
}
