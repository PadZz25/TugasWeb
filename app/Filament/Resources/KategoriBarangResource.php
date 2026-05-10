<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KategoriBarangResource\Pages;
use App\Filament\Resources\KategoriBarangResource\RelationManagers;
use App\Models\KategoriBarang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KategoriBarangResource extends Resource
{
    protected static ?string $model = KategoriBarang::class;
    protected static ?string $modelLabel = 'Kategori Barang';
    protected static ?string $pluralModelLabel = 'Kategori Barang';
    protected static ?string $navigationGroup = 'Management';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
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
                \Filament\Forms\Components\TextInput::make('nama')
                    ->label('Nama Kategori')
                    ->placeholder('Contoh: Sembako, Camilan, dll.')
                    ->required()
                    ->unique(ignoreRecord: true) // Biar gak ada kategori ganda
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('id_kategori')
                    ->label('ID')
                    ->sortable(),
                    
                \Filament\Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Kategori')
                    ->searchable()
                    ->sortable(),
                
                // Tambahan: Biar ketahuan tiap kategori ada berapa barang
                \Filament\Tables\Columns\TextColumn::make('barang_count')
                    ->label('Jumlah Barang')
                    ->counts('barang'),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Tables\Actions\EditAction::make(),
                \Filament\Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\BulkActionGroup::make([
                    \Filament\Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageKategoriBarangs::route('/'),
        ];
    }
}
