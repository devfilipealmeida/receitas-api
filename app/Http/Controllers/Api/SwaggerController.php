<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class SwaggerController extends Controller
{
    public function __invoke(): Response
    {
        $specUrl = '/api/docs/spec';
        $html = <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receitas API - Documentação</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5.11.0/swagger-ui.css">
</head>
<body>
    <div id="swagger-ui"></div>
    <script src="https://unpkg.com/swagger-ui-dist@5.11.0/swagger-ui-bundle.js" crossorigin></script>
    <script>
        window.onload = () => {
            window.ui = SwaggerUIBundle({
                url: "{$specUrl}",
                dom_id: '#swagger-ui',
                deepLinking: true,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                plugins: [
                    SwaggerUIBundle.plugins.DownloadUrl
                ],
                layout: "StandaloneLayout"
            });
        };
    </script>
    <script src="https://unpkg.com/swagger-ui-dist@5.11.0/swagger-ui-standalone-preset.js" crossorigin></script>
</body>
</html>
HTML;

        return response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    public function spec(): Response
    {
        $path = storage_path('api-docs/openapi.yaml');
        if (! File::exists($path)) {
            abort(404);
        }
        $content = File::get($path);

        return response($content, 200, [
            'Content-Type' => 'application/x-yaml',
            'Content-Disposition' => 'inline; filename="openapi.yaml"',
        ]);
    }
}
