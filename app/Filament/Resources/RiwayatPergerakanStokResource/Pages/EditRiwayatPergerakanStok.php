<?php

namespace App\Filament\Resources\RiwayatPergerakanStokResource\Pages;

use App\Filament\Resources\RiwayatPergerakanStokResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRiwayatPergerakanStok extends EditRecord
{
    protected static string $resource = RiwayatPergerakanStokResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
