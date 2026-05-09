<?php

namespace App\Filament\Resources\KatalogBarangResource\Pages;

use App\Filament\Resources\KatalogBarangResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKatalogBarangs extends ListRecords
{
    protected static string $resource = KatalogBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
