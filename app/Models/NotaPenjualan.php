<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotaPenjualan extends Model
{
    protected $table = 'nota_penjualan';
    protected $primaryKey = 'id_nota';
    public $timestamps = false;
    protected $guarded = [];

    // Relasi ke Kasir/Karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }

    // Relasi ke Pelanggan
    public function pelanggan()
    {
        return $this->belongsTo(DataPelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    // Relasi ke Item Belanjaan (1 Nota punya banyak Item)
    public function item()
    {
        return $this->hasMany(ItemPenjualan::class, 'id_nota', 'id_nota');
    }
}