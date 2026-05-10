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

    // ... relasi yang sebelumnya udah ada (karyawan, pelanggan, item)

    // CCTV buat mantau tiap ada transaksi baru yang disimpan
    protected static function booted()
    {
        static::created(function ($nota) {
            // Kalau metode bayarnya 'hutang' dan kasir milih pelanggannya
            if ($nota->metode_bayar === 'hutang' && $nota->id_pelanggan != null) {
                
                // Panggil data pelanggannya
                $pelanggan = $nota->pelanggan;
                
                // Tambahin total tagihan nota ini ke total hutang pelanggan
                $pelanggan->total_hutang += $nota->total_akhir;
                $pelanggan->save();
            }
        });
    }

}