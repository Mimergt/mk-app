<?php

namespace App\Filament\Pages\Analisis;

use Filament\Pages\Page;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Principal extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Principal';

    protected static ?string $title = 'Análisis - Principal';

    protected static ?string $navigationGroup = 'Análisis';

    protected static ?int $navigationSort = 0;

    protected static string $view = 'filament.pages.analisis.principal';

    protected static ?string $slug = 'analisis/principal';

    public function getMetabaseUrl(): string
    {
        $metabaseSiteUrl = env('METABASE_SITE_URL', 'https://mk.epicdeploy.com');
        $secretKey = env('METABASE_SECRET_KEY');

        if (!$secretKey) {
            throw new \Exception('METABASE_SECRET_KEY not configured in .env file');
        }

        // Dashboard ID 65 as provided by Metabase
        $dashboardId = 65;

        $payload = [
            'resource' => ['dashboard' => $dashboardId],
            'params' => (object)[], // Usar object vacío para permitir acceso completo
            'exp' => time() + (10 * 60), // 10 minute expiration as per Metabase code
            'iat' => time() // Issued at time
        ];

        $token = JWT::encode($payload, $secretKey, 'HS256');

        // Debug: Log the token and URL for troubleshooting
        \Log::info('Metabase Token Generated', [
            'dashboard_id' => $dashboardId,
            'token' => $token,
            'url' => $metabaseSiteUrl . "/embed/dashboard/" . $token
        ]);

        return $metabaseSiteUrl . "/embed/dashboard/" . $token . "#bordered=false&titled=true";
    }
}