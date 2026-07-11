<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Data_barang extends Model
{
    protected $guarded = [];
    protected $table = 'data_barang';

    public function pembelian()
    {
        return $this->hasMany(PembelianBarang::class, 'id_barang');
    }
}
