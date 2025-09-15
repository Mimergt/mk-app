<?php

namespace App\Filament\Pages\Analisis;

use Filament\Pages\Page;

class Tabla extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    protected static ?string $navigationLabel = 'Tabla';

    protected static ?string $title = 'Análisis - Tabla';

    protected static ?string $navigationGroup = 'Análisis';

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.analisis.tabla';

    protected static ?string $slug = 'analisis/tabla';

    protected static bool $shouldRegisterNavigation = false; // Desactivada según requerimiento
}