<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Utang extends Model
{
    protected $table = 'utangs';

    protected $fillable = [
        'nama_pelanggan',
        'keterangan_barang',
        'total_utang',
        'status',
    ];
}
