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
            'nama' => 'Casing iPhone 17 Pro Max',
            'qty' => '100',
            'harga_modal' => '20000',
            'barcode' => '100000000001',
            'harga_umum' => '30000',
            'harga_member' => '28000',
        ]);

        Data_barang::create([
            'id_toko' => '1',
            'id_supplier' => '1',
            'nama' => 'Tempered Glass iPhone',
            'qty' => '200',
            'harga_modal' => '5000',
            'barcode' => '100000000002',
            'harga_umum' => '10000',
            'harga_member' => '9000',
        ]);

        Data_barang::create([
            'id_toko' => '1',
            'id_supplier' => '1',
            'nama' => 'Charger Fast Charging 20W',
            'qty' => '150',
            'harga_modal' => '25000',
            'barcode' => '100000000003',
            'harga_umum' => '40000',
            'harga_member' => '38000',
        ]);

        Data_barang::create([
            'id_toko' => '1',
            'id_supplier' => '1',
            'nama' => 'Kabel Data Type-C',
            'qty' => '300',
            'harga_modal' => '8000',
            'barcode' => '100000000004',
            'harga_umum' => '15000',
            'harga_member' => '13000',
        ]);

        Data_barang::create([
            'id_toko' => '1',
            'id_supplier' => '1',
            'nama' => 'Headset Bluetooth',
            'qty' => '120',
            'harga_modal' => '35000',
            'barcode' => '100000000005',
            'harga_umum' => '60000',
            'harga_member' => '55000',
        ]);

        Data_barang::create([
            'id_toko' => '1',
            'id_supplier' => '1',
            'nama' => 'Powerbank 10000mAh',
            'qty' => '80',
            'harga_modal' => '70000',
            'barcode' => '100000000006',
            'harga_umum' => '100000',
            'harga_member' => '95000',
        ]);

        Data_barang::create([
            'id_toko' => '1',
            'id_supplier' => '1',
            'nama' => 'Holder HP Motor',
            'qty' => '90',
            'harga_modal' => '15000',
            'barcode' => '100000000007',
            'harga_umum' => '25000',
            'harga_member' => '23000',
        ]);

        Data_barang::create([
            'id_toko' => '1',
            'id_supplier' => '1',
            'nama' => 'Ring Holder HP',
            'qty' => '200',
            'harga_modal' => '3000',
            'barcode' => '100000000008',
            'harga_umum' => '7000',
            'harga_member' => '6000',
        ]);

        Data_barang::create([
            'id_toko' => '1',
            'id_supplier' => '1',
            'nama' => 'Adapter Charger USB',
            'qty' => '140',
            'harga_modal' => '20000',
            'barcode' => '100000000009',
            'harga_umum' => '35000',
            'harga_member' => '32000',
        ]);

        Data_barang::create([
            'id_toko' => '1',
            'id_supplier' => '1',
            'nama' => 'Kartu Perdana Internet',
            'qty' => '500',
            'harga_modal' => '5000',
            'barcode' => '100000000010',
            'harga_umum' => '10000',
            'harga_member' => '9500',
        ]);
    }
}
