<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KaryawanResource\Pages;
use App\Filament\Resources\KaryawanResource\RelationManagers;
use App\Models\Karyawan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KaryawanResource extends Resource
{
    protected static ?string $model = Karyawan::class;
    protected static ?string $modelLabel = 'Karyawan';
    protected static ?string $pluralModelLabel = 'Karyawan';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\TextInput::make('nama')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255),
                    
                \Filament\Forms\Components\TextInput::make('username')
                    ->label('Username (Buat Login)')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                    
                \Filament\Forms\Components\Select::make('peran')
                    ->label('Peran / Jabatan')
                    ->options([
                        'admin' => 'Admin',
                        'kasir' => 'Kasir',
                    ])
                    ->required(),

                \Filament\Forms\Components\TextInput::make('password_hash')
                    ->label('Password')
                    ->password()
                    ->revealable() // Biar ada tombol intip mata
                    ->required(fn (\Filament\Resources\Pages\Page $livewire): bool => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                    ->dehydrateStateUsing(fn ($state) => \Illuminate\Support\Facades\Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->helperText('Kosongkan jika tidak ingin mengubah password saat edit data.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                    
                \Filament\Tables\Columns\TextColumn::make('username')
                    ->label('Username')
                    ->searchable(),
                    
                \Filament\Tables\Columns\TextColumn::make('peran')
                    ->label('Jabatan')
                    ->badge() // Biar tampilannya kayak tombol warna-warni
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger', // Admin warnanya merah
                        'kasir' => 'success', // Kasir warnanya ijo
                        default => 'gray',
                    }),
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
            'index' => Pages\ListKaryawans::route('/'),
            'create' => Pages\CreateKaryawan::route('/create'),
            'edit' => Pages\EditKaryawan::route('/{record}/edit'),
        ];
    }
}
