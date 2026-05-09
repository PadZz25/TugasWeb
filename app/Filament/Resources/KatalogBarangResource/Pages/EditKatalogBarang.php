<?php

namespace App\Filament\Resources\KatalogBarangResource\Pages;

use App\Filament\Resources\KatalogBarangResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKatalogBarang extends EditRecord
{
    protected static string $resource = KatalogBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
