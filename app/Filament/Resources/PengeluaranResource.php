<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengeluaranResource\Pages;
use App\Filament\Resources\PengeluaranResource\RelationManagers;
use App\Models\Pengeluaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PengeluaranResource extends Resource
{
    protected static ?string $model = Pengeluaran::class;
    protected static ?string $navigationGroup = 'Transaksi & keuangan';

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
                // Pakai DateTimePicker karena di database formatnya Y-m-d H:i:s
                \Filament\Forms\Components\DateTimePicker::make('tanggal')
                    ->label('Tanggal & Waktu')
                    ->default(now())
                    ->required(),
                    
                \Filament\Forms\Components\TextInput::make('jumlah')
                    ->label('Nominal Pengeluaran')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
                    
                // Disesuaikan sama nama kolom di databasemu
                \Filament\Forms\Components\TextInput::make('nama_pengeluaran')
                    ->label('Keterangan Pengeluaran')
                    ->placeholder('Contoh: Iuran Sampah & Keamanan')
                    ->required()
                    ->columnSpanFull(),
                    
                \Filament\Forms\Components\Hidden::make('id_karyawan')
                    ->default(fn () => auth()->user()->id_karyawan),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('tanggal')
                    ->label('Waktu')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                    
                \Filament\Tables\Columns\TextColumn::make('nama_pengeluaran')
                    ->label('Keterangan')
                    ->searchable(),
                    
                \Filament\Tables\Columns\TextColumn::make('jumlah')
                    ->label('Total Nominal')
                    ->money('IDR')
                    ->sortable()
                    ->weight('bold')
                    // Fitur ngitung total pengeluaran otomatis
                    ->summarize(\Filament\Tables\Columns\Summarizers\Sum::make()->label('Total Keseluruhan')),
                    
                \Filament\Tables\Columns\TextColumn::make('karyawan.nama')
                    ->label('Dicatat Oleh'),
            ])
            ->defaultSort('tanggal', 'desc')
            ->actions([
                \Filament\Tables\Actions\EditAction::make(),
                \Filament\Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListPengeluarans::route('/'),
            'create' => Pages\CreatePengeluaran::route('/create'),
            'edit' => Pages\EditPengeluaran::route('/{record}/edit'),
        ];
    }
}
