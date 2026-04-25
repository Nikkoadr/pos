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
            'id_toko' => 1,
            'id_supplier'     => 1,
            'barcode'     => $row['barcode'],
            'nama'     => $row['nama_barang'],
            'qty'     => $row['qty'],
            'harga_modal'     => $row['harga_modal'],
            'harga_umum'     => $row['harga_umum'],
            'harga_member'     => $row['harga_member'],
        ]);
    }
}
