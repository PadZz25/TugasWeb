<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Models\NotaPenjualan;

// Route untuk halaman cetak struk
Route::get('/cetak-struk/{id}', function ($id) {
    // Tarik data nota beserta relasinya (karyawan, pelanggan, dan item -> barang)
    $nota = NotaPenjualan::with(['karyawan', 'pelanggan', 'item.barang'])->findOrFail($id);
    
    return view('cetak-struk', compact('nota'));
})->name('cetak.struk');
