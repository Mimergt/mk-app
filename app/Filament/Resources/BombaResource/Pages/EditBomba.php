<?php

namespace App\Filament\Resources\BombaResource\Pages;

use App\Filament\Resources\BombaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBomba extends EditRecord
{
    protected static string $resource = BombaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
