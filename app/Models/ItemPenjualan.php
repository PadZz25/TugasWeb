<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemPenjualan extends Model
{
    protected $table = 'item_penjualan';
    protected $primaryKey = 'id_item_penjualan';
    public $timestamps = false;
    protected $guarded = [];

    // Relasi balik ke Nota
    public function nota()
    {
        return $this->belongsTo(NotaPenjualan::class, 'id_nota', 'id_nota');
    }

    // Relasi ke Barang
    public function barang()
    {
        return $this->belongsTo(KatalogBarang::class, 'id_barang', 'id_barang');
    }
}