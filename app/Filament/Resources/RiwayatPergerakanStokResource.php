<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RiwayatPergerakanStokResource\Pages;
use App\Filament\Resources\RiwayatPergerakanStokResource\RelationManagers;
use App\Models\RiwayatPergerakanStok;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RiwayatPergerakanStokResource extends Resource
{
    protected static ?string $model = RiwayatPergerakanStok::class;
    protected static ?string $navigationGroup = 'Laporan & Analitik';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function canViewAny(): bool
    {
        // Cuma karyawan dengan peran 'admin' yang boleh lihat menu ini
        return auth()->user()->peran === 'admin'; 
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\DateTimePicker::make('tanggal')
                    ->label('Waktu Kejadian')
                    ->default(now())
                    ->required(),
                    
                \Filament\Forms\Components\Select::make('id_barang')
                    ->relationship('barang', 'nama')
                    ->label('Pilih Barang')
                    ->searchable()
                    ->required(),
                    
                \Filament\Forms\Components\Select::make('jenis_pergerakan')
                    ->label('Jenis Pergerakan')
                    ->options([
                        'masuk' => 'Masuk (Kulakan)',
                        'keluar' => 'Keluar (Terjual/Rusak)',
                        'penyesuaian' => 'Penyesuaian Manual',
                    ])
                    ->required(),
                    
                \Filament\Forms\Components\TextInput::make('jumlah_perubahan')
                    ->label('Jumlah Perubahan')
                    ->numeric()
                    ->helperText('Bisa diisi minus (contoh: -2) kalau barang keluar/rusak')
                    ->required(),
                    
                \Filament\Forms\Components\TextInput::make('sisa_stok')
                    ->label('Sisa Stok (Opsional)')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('tanggal')
                    ->label('Waktu Kejadian')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                    
                \Filament\Tables\Columns\TextColumn::make('barang.nama')
                    ->label('Nama Barang')
                    ->searchable(),
                    
                \Filament\Tables\Columns\TextColumn::make('jenis_pergerakan')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'masuk' => 'success',
                        'keluar' => 'danger',
                        'penyesuaian' => 'warning',
                        default => 'secondary', // Buat jaga-jaga kalau ada isi lain
                    }),
                    
                \Filament\Tables\Columns\TextColumn::make('jumlah_perubahan')
                    ->label('Perubahan')
                    ->numeric()
                    ->alignCenter()
                    ->weight('bold'),
                    
                \Filament\Tables\Columns\TextColumn::make('sisa_stok')
                    ->label('Sisa Stok')
                    ->numeric()
                    ->alignCenter(),
            ])
            ->defaultSort('tanggal', 'desc')
            ->actions([
                // Riwayat biasanya nggak boleh diedit/dihapus sembarangan, tapi kita kasih tombol View aja biar aman
                \Filament\Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListRiwayatPergerakanStoks::route('/'),
            'create' => Pages\CreateRiwayatPergerakanStok::route('/create'),
            'edit' => Pages\EditRiwayatPergerakanStok::route('/{record}/edit'),
        ];
    }
}
