<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\NotaPenjualan;
use Carbon\Carbon;

class PenjualanChart extends ChartWidget
{
    // Judul di atas grafiknya
    protected static ?string $heading = 'Grafik Transaksi 7 Hari Terakhir';
    
    // Urutan widget (1 = paling atas di bawah deretan angka)
    protected static ?int $sort = 1; 

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        // Looping mundur dari 6 hari yang lalu sampai hari ini
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            // Masukin nama hari ke sumbu X (contoh: Senin, Selasa)
            $labels[] = $date->translatedFormat('l'); 
            
            // Hitung ada berapa nota_penjualan di tanggal tersebut
            // Asumsi nama kolom tanggalmu adalah 'tanggal'
            $jumlahTransaksi = NotaPenjualan::whereDate('tanggal', $date->toDateString())->count();
            $data[] = $jumlahTransaksi;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Transaksi',
                    'data' => $data,
                    'borderColor' => '#3b82f6', // Warna garis biru keren
                    'fill' => 'start',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line'; // Bisa juga diubah jadi 'bar' kalau mau bentuk batang
    }
}