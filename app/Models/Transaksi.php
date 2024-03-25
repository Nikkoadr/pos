<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Data_member;

class Transaksi extends Model
{
    protected $guarded = [];
    protected $table = 'transaksi';

    public function member()
    {
        return $this->belongsTo(Data_member::class, 'member_id');
    }
}
