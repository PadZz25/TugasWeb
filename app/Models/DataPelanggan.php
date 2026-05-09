<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataPelanggan extends Model
{
    protected $table = 'data_pelanggan';
    protected $primaryKey = 'id_pelanggan';
    public $timestamps = false;
    protected $guarded = [];

    // Relasi ke nota (1 pelanggan bisa punya banyak nota)
    public function nota()
    {
        return $this->hasMany(NotaPenjualan::class, 'id_pelanggan', 'id_pelanggan');
    }
}