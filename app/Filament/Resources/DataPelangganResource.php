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
                    ->money('IDR') // Langsung format Rupiah
                    ->sortable()
                    ->color(fn ($state): string => $state > 0 ? 'danger' : 'success') // Merah kalau ada utang, hijau kalau lunas
                    ->weight('bold'),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Tables\Actions\EditAction::make(),
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
