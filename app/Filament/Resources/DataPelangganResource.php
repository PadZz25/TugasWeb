<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DataPelangganResource\Pages;
use App\Filament\Resources\DataPelangganResource\RelationManagers;
use App\Models\DataPelanggan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DataPelangganResource extends Resource
{
    protected static ?string $model = DataPelanggan::class;
    protected static ?string $modelLabel = 'Data Pelanggan';
    protected static ?string $pluralModelLabel = 'Data Pelanggan';
    protected static ?string $navigationGroup = 'management kontak';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\TextInput::make('nama')
                    ->label('Nama Pelanggan')
                    ->required()
                    ->maxLength(255),
                    
                \Filament\Forms\Components\TextInput::make('telepon')
                    ->label('Nomor Telepon')
                    ->tel()
                    ->maxLength(255),
                    
                \Filament\Forms\Components\TextInput::make('total_hutang')
                    ->label('Total Hutang')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0)
                    ->readOnly() // Dikunci! Biar kasir nggak bisa ngedit utang sembarangan
                    ->helperText('Hutang otomatis ter-update dari transaksi sistem.'),
            ]);
    }

   public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Pelanggan')
                    ->searchable()
                    ->sortable(),
                    
                \Filament\Tables\Columns\TextColumn::make('telepon')
                    ->label('No. Telepon')
                    ->searchable(),
                    
                \Filament\Tables\Columns\TextColumn::make('total_hutang')
                    ->label('Total Hutang')
                    ->money('IDR')
                    ->sortable()
                    ->color(fn ($state): string => $state > 0 ? 'danger' : 'success')
                    ->weight('bold'),
            ])
            ->filters([
                //
            ])
            ->actions([
                // TOMBOL EDIT BAWAAN
                \Filament\Tables\Actions\EditAction::make(),
                
                // TOMBOL BARU: BAYAR HUTANG
                \Filament\Tables\Actions\Action::make('bayar_hutang')
                    ->label('Bayar Hutang')
                    ->icon('heroicon-m-banknotes') // Kasih icon duit
                    ->color('success') // Kasih warna ijo biar seger
                    ->visible(fn ($record) => $record->total_hutang > 0) // Cuma muncul kalau utangnya lebih dari 0
                    ->form([
                        \Filament\Forms\Components\TextInput::make('nominal_bayar')
                            ->label('Nominal Pembayaran')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            // Kasir gak bisa masukin angka lebih gede dari sisa utang
                            ->maxValue(fn ($record) => $record->total_hutang) 
                            ->helperText(fn ($record) => 'Sisa hutang saat ini: Rp ' . number_format($record->total_hutang, 0, ',', '.')),
                    ])
                    ->action(function ($record, array $data) {
                        // 1. Logic kamu yang lama buat ngurangin saldo hutang di tabel pelanggan (tetep biarin)
                        $record->update([
                            'total_hutang' => $record->total_hutang - $data['nominal_bayar'],
                        ]);

                         // 2. LOGIC TAMBAHAN: Update status di tabel Nota Penjualan
                        // Kita cari semua nota milik pelanggan ini yang statusnya masih 'belum_lunas'
                        \App\Models\NotaPenjualan::where('id_pelanggan', $record->id_pelanggan)
                            ->where('status_bayar', 'belum_lunas')
                            ->update(['status_bayar' => 'lunas']);

                        // Kasih notifikasi biar asik
                        \Filament\Notifications\Notification::make()
                            ->title('Hutang Dibayar & Status Nota Terupdate!')
                            ->success()
                            ->send();
                    })
            ])
            ->bulkActions([
                \Filament\Tables\Actions\BulkActionGroup::make([
                    \Filament\Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDataPelanggans::route('/'),
            'create' => Pages\CreateDataPelanggan::route('/create'),
            'edit' => Pages\EditDataPelanggan::route('/{record}/edit'),
        ];
    }
}
