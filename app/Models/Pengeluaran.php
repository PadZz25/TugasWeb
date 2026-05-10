<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    // Arahin ke tabel database aslimu
    protected $table = 'pengeluaran_toko'; 
    protected $primaryKey = 'id_pengeluaran';
    
    // Matiin timestamps karena di tabelmu nggak ada created_at & updated_at
    public $timestamps = false; 
    
    protected $guarded = [];

    // Relasi ke Kasir/Karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }
}