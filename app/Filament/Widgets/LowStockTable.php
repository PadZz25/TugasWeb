<?php

namespace App\Filament\Widgets;

use App\Models\KatalogBarang;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockTable extends BaseWidget
{
    // Bikin judul tabelnya
    protected static ?string $heading = 'Peringatan Stok Menipis';
    
    // Bikin tabelnya ngelebar full dari ujung ke ujung
    protected int | string | array $columnSpan = 'full'; 

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Kunci utamanya di sini: cuma nampilin barang yang stoknya di bawah 10
                KatalogBarang::where('stok_sekarang', '<', 10) 
            )
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Barang')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('stok_sekarang')
                    ->label('Sisa Stok')
                    ->badge()
                    ->color('danger') // Warnain merah biar kasir langsung notice
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('satuan')
                    ->label('Satuan'),
            ]);
    }
}