<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    protected $guarded = [];
    protected $table = 'nota';

    public function detailNota()
    {
        return $this->hasMany(Detail_nota::class, 'id_nota');
    }
}
