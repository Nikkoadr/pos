<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Data_barang;

class Import_data_barang implements ToModel, WithHeadingRow
{
    /**
     * Bersihkan format rupiah
     */
    private function cleanRupiah($value)
    {
        if (!$value) {
            return 0;
        }

        // ubah ke string
        $value = (string) $value;

        // hapus Rp, titik, koma, dan spasi
        $value = preg_replace('/[^0-9]/', '', $value);

        return (int) $value;
    }

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Data_barang([
            'id_toko'       => 1,
            'id_supplier'   => 1,
            'barcode'       => $row['barcode'],
            'nama'          => $row['nama_barang'],
            'qty'           => $row['qty'],

            'harga_modal'   => $this->cleanRupiah($row['harga_modal']),
            'harga_umum'    => $this->cleanRupiah($row['harga_umum']),
            'harga_member'  => $this->cleanRupiah($row['harga_member']),
        ]);
    }
}
