<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KatalogBarangResource\Pages;
use App\Filament\Resources\KatalogBarangResource\RelationManagers;
use App\Models\KatalogBarang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KatalogBarangResource extends Resource
{
    protected static ?string $model = KatalogBarang::class;
    protected static ?string $modelLabel = 'Katalog Barang';
    protected static ?string $pluralModelLabel = 'Katalog Barang';
    protected static ?string $navigationGroup = 'Management';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    // Biar Kasir & Admin sama-sama bisa buka menu Katalog
    public static function canViewAny(): bool
    {
        return true; 
    }

    // CUMA ADMIN yang boleh nambah barang baru
    public static function canCreate(): bool
    {
        return auth()->user()->peran === 'admin';
    }

    // CUMA ADMIN yang boleh ngedit barang (termasuk ngubah stok manual)
    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()->peran === 'admin';
    }

    // CUMA ADMIN yang boleh hapus barang
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()->peran === 'admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Bikin dropdown buat milih Kategori
                \Filament\Forms\Components\Select::make('id_kategori')
                    ->relationship('kategori', 'nama') // Ngambil dari relasi di Model
                    ->label('Kategori')
                    ->required(),
                    
                \Filament\Forms\Components\TextInput::make('nama')
                    ->label('Nama Barang')
                    ->required()
                    ->maxLength(255),
                    
                \Filament\Forms\Components\TextInput::make('barcode')
                    ->label('Barcode / Kode Barang')
                    ->maxLength(255),
                    
                \Filament\Forms\Components\TextInput::make('satuan')
                    ->label('Satuan (Pcs/Dus/Kg)'),
                    
                \Filament\Forms\Components\TextInput::make('harga_beli')
                    ->label('Harga Beli (HPP)')
                    ->numeric()
                    ->prefix('Rp'),
                    
                \Filament\Forms\Components\TextInput::make('harga_jual')
                    ->label('Harga Jual')
                    ->numeric()
                    ->prefix('Rp'),
                    
                \Filament\Forms\Components\TextInput::make('stok_sekarang')
                    ->label('Stok Saat Ini')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('barcode')
                    ->label('Barcode')
                    ->searchable(), // Biar bisa dicari
                    
                \Filament\Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(), // Biar bisa diurutin abjad
                    
                \Filament\Tables\Columns\TextColumn::make('kategori.nama')
                    ->label('Kategori')
                    ->sortable(),
                    
                \Filament\Tables\Columns\TextColumn::make('harga_jual')
                    ->label('Harga Jual')
                    ->money('IDR') // Langsung diformat jadi Rupiah!
                    ->sortable(),
                    
                \Filament\Tables\Columns\TextColumn::make('stok_sekarang')
                    ->label('Sisa Stok')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                // Nanti kita bisa nambahin filter di sini
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(), // Tambahin tombol hapus sekalian
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListKatalogBarangs::route('/'),
            'create' => Pages\CreateKatalogBarang::route('/create'),
            'edit' => Pages\EditKatalogBarang::route('/{record}/edit'),
        ];
    }
}
