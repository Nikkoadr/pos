<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Data_barang;

class Import_data_barang implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Data_barang([
            'id_toko' => $row['id_toko'],
            'id_supplier'     => $row['id_supplier'],
            'nama'     => $row['nama_barang'],
            'qty'     => $row['stok'],
            'harga_modal'     => $row['harga_modal'],
            'harga_jual1'     => $row['harga_umum'],
            'harga_jual2'     => $row['harga_grosir'],
        ]);
    }
}
