<?php

namespace App\Filament\Pages\Analisis;

use Filament\Pages\Page;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Detalles extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected static ?string $navigationLabel = 'Detalles';

    protected static ?string $title = 'Análisis - Detalles';

    protected static ?string $navigationGroup = 'Análisis';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.analisis.detalles';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    protected static ?string $slug = 'analisis/detalles';

    public function getMetabaseUrl(): string
    {
        $metabaseSiteUrl = env('METABASE_SITE_URL', 'https://mk.epicdeploy.com');
        $secretKey = env('METABASE_SECRET_KEY');

        if (!$secretKey) {
            throw new \Exception('METABASE_SECRET_KEY not configured in .env file');
        }

        // El ID del dashboard debe ser numérico, no el hash de embedding
        // Cambia este número por el ID real de tu dashboard (ejemplo: 1, 2, 3, etc.)
        $dashboardId = 35; // CAMBIAR POR EL ID REAL DEL DASHBOARD

        $payload = [
            'resource' => ['dashboard' => $dashboardId],
            'params' => (object)[], // Usar object vacío para permitir acceso completo
            'exp' => time() + (60 * 60), // 1 hour expiration
            'iat' => time() // Issued at time
        ];

        $token = JWT::encode($payload, $secretKey, 'HS256');

        // Debug: Log the token and URL for troubleshooting
        \Log::info('Metabase Token Generated', [
            'dashboard_id' => $dashboardId,
            'token' => $token,
            'url' => $metabaseSiteUrl . "/embed/dashboard/" . $token
        ]);

        return $metabaseSiteUrl . "/embed/dashboard/" . $token . "#bordered=true&titled=true&theme=light";
    }
}