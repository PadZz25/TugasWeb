<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KatalogBarang extends Model
{
    protected $table = 'katalog_barang';
    protected $primaryKey = 'id_barang';
    public $timestamps = false;
    protected $guarded = [];

    // Relasi balik ke kategori (1 barang punya 1 kategori)
    public function kategori()
    {
        return $this->belongsTo(KategoriBarang::class, 'id_kategori', 'id_kategori');
    }
}