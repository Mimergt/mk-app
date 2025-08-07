<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class PanelTurnos extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-arrow-top-right-on-square';
    
    protected static ?string $navigationLabel = 'Panel de Turnos';
    
    protected static ?string $title = 'Panel de Turnos';
    
    protected static ?string $navigationGroup = 'Operación';
    
    protected static ?int $navigationSort = 100;

    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?\Illuminate\Database\Eloquent\Model $tenant = null): string
    {
        return url('/gas');
    }
    
    public static function shouldOpenUrlInNewTab(): bool
    {
        return true;
    }
    
    // Esta página redirige directamente, no necesita vista
    public function mount(): void
    {
        redirect('/gas');
    }
}
