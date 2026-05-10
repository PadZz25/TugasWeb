<?php

namespace App\Filament\Widgets;

use App\Models\NotaPenjualan;
use App\Models\KatalogBarang;
use App\Models\DataPelanggan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Narik data omzet khusus hari ini aja dari tabel nota_penjualan
        $omzetHariIni = NotaPenjualan::whereDate('tanggal', Carbon::today())
            ->where('status_bayar', 'lunas') // <-- INI TAMBAHANNYA
            ->sum('total_akhir');
        
        // Ngitung ada berapa barang yang stoknya udah di bawah 10
        $stokMenipis = KatalogBarang::where('stok_sekarang', '<', 10)->count();

        // Ngitung total orang yang udah terdaftar jadi pelanggan
        $totalPelanggan = DataPelanggan::count();

        return [
            Stat::make('Omzet Hari Ini', 'Rp ' . number_format($omzetHariIni, 0, ',', '.'))
                ->description('Total transaksi masuk hari ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
                
            Stat::make('Peringatan Stok', $stokMenipis . ' Produk')
                ->description('Barang dengan stok di bawah 10')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($stokMenipis > 0 ? 'danger' : 'success'),
                
            Stat::make('Total Pelanggan', $totalPelanggan . ' Orang')
                ->description('Jumlah pelanggan/member terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
        ];
    }
}