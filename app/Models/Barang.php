<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barangs';

    protected $fillable = [
        'nama_barang',
        'harga_beli',
        'harga_jual',
        'stok',
        'min_stok',
    ];
}
