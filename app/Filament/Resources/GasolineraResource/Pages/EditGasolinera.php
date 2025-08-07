<?php

namespace App\Filament\Resources\GasolineraResource\Pages;

use App\Filament\Resources\GasolineraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGasolinera extends EditRecord
{
    protected static string $resource = GasolineraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
