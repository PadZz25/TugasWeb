<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatPergerakanStok extends Model
{
    // Kasih tahu Laravel kalau nama tabelnya beda dari standar
    protected $table = 'riwayat_pergerakan_stok';
    protected $primaryKey = 'id_riwayat';
    
    // Matiin timestamps karena biasanya tabel riwayat cuma pakai kolom tanggal/waktu manual
    public $timestamps = false; 
    
    protected $guarded = [];

    // Relasi ke barang, biar kita tahu stok apa yang gerak
    public function barang()
    {
        return $this->belongsTo(KatalogBarang::class, 'id_barang', 'id_barang');
    }
}