<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriBarang extends Model
{
    protected $table = 'kategori_barang';
    protected $primaryKey = 'id_kategori';
    public $timestamps = false;
    protected $guarded = [];

    // Relasi ke barang (1 kategori punya banyak barang)
    public function barang()
    {
        return $this->hasMany(KatalogBarang::class, 'id_kategori', 'id_kategori');
    }
}