<?php

namespace App\Filament\Resources\GasolineraResource\Pages;

use App\Filament\Resources\GasolineraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGasolineras extends ListRecords
{
    protected static string $resource = GasolineraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
