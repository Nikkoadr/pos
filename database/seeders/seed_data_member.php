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
            'id_toko' => '1',
            'nama_member' => 'Budi',
            'nomor_hp' => '081000000002',
            'alamat' => 'Indramayu',
        ]);

        Data_member::create([
            'id_toko' => '1',
            'nama_member' => 'Siti',
            'nomor_hp' => '081000000003',
            'alamat' => 'Cirebon',
        ]);

        Data_member::create([
            'id_toko' => '1',
            'nama_member' => 'Rina',
            'nomor_hp' => '081000000004',
            'alamat' => 'Jatibarang',
        ]);

        Data_member::create([
            'id_toko' => '1',
            'nama_member' => 'Dedi',
            'nomor_hp' => '081000000005',
            'alamat' => 'Karangampel',
        ]);

        Data_member::create([
            'id_toko' => '1',
            'nama_member' => 'Andi',
            'nomor_hp' => '081000000006',
            'alamat' => 'Majalengka',
        ]);

        Data_member::create([
            'id_toko' => '1',
            'nama_member' => 'Putri',
            'nomor_hp' => '081000000007',
            'alamat' => 'Kuningan',
        ]);

        Data_member::create([
            'id_toko' => '1',
            'nama_member' => 'Rizky',
            'nomor_hp' => '081000000008',
            'alamat' => 'Subang',
        ]);

        Data_member::create([
            'id_toko' => '1',
            'nama_member' => 'Wulan',
            'nomor_hp' => '081000000009',
            'alamat' => 'Pamanukan',
        ]);

        Data_member::create([
            'id_toko' => '1',
            'nama_member' => 'Fajar',
            'nomor_hp' => '081000000010',
            'alamat' => 'Haurgeulis',
        ]);

        Data_member::create([
            'id_toko' => '1',
            'nama_member' => 'Lina',
            'nomor_hp' => '081000000011',
            'alamat' => 'Losarang',
        ]);
    }
}
