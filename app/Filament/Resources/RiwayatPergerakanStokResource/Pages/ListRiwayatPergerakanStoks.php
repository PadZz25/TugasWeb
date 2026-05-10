<?php

namespace App\Filament\Resources\RiwayatPergerakanStokResource\Pages;

use App\Filament\Resources\RiwayatPergerakanStokResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRiwayatPergerakanStoks extends ListRecords
{
    protected static string $resource = RiwayatPergerakanStokResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
