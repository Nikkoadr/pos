<?php

namespace App\Exports;

use App\Models\Data_barang;
use Maatwebsite\Excel\Concerns\FromCollection;

class Export_data_barang implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Data_barang::all();
    }
}
