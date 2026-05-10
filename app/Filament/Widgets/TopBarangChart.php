<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\ItemPenjualan;
use Illuminate\Support\Facades\DB;

class TopBarangChart extends ChartWidget
{
    protected static ?string $heading = '5 Barang Paling Laris';
    
    // Taruh di urutan kedua setelah grafik transaksi
    protected static ?int $sort = 2; 

    protected function getData(): array
    {
        // Query buat nyari 5 barang dengan jumlah penjualan terbanyak
        $topBarang = ItemPenjualan::query()
            ->select('id_barang', DB::raw('SUM(jumlah) as total_terjual'))
            ->groupBy('id_barang')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        $data = [];
        $labels = [];

        foreach ($topBarang as $item) {
            // Ambil nama barangnya (asumsi relasi di model ItemPenjualan namanya 'barang')
            $labels[] = $item->barang->nama ?? 'Unknown';
            $data[] = $item->total_terjual;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Terjual',
                    'data' => $data,
                    'backgroundColor' => [
                        '#f59e0b', // Amber
                        '#10b981', // Emerald
                        '#3b82f6', // Blue
                        '#8b5cf6', // Violet
                        '#ef4444', // Red
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}