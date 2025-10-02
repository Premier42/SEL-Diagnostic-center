<?php
/**
 * Vite Helper Functions
 * Handles loading Vite assets in development and production
 */

/**
 * Check if Vite dev server is running
 */
function isViteDevServerRunning(): bool {
    $viteDevServer = 'http://localhost:5173';
    $ch = curl_init($viteDevServer);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    curl_exec($ch);
    $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $responseCode === 200;
}

/**
 * Generate Vite asset tags
 * In dev: loads from Vite dev server with HMR
 * In prod: loads from built assets
 */
function viteAssets(): string {
    $isDev = isViteDevServerRunning();

    if ($isDev) {
        // Development mode - load from Vite dev server
        return <<<HTML
        <script type="module" src="http://localhost:5173/@vite/client"></script>
        <script type="module" src="http://localhost:5173/resources/js/main.js"></script>
        HTML;
    } else {
        // Production mode - load built assets
        $manifestPath = __DIR__ . '/../public/dist/manifest.json';

        if (!file_exists($manifestPath)) {
            return '<!-- Vite: Run "npm run build" to generate assets -->';
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);

        $jsFile = '/dist/' . $manifest['resources/js/main.js']['file'];
        $cssFile = '/dist/' . $manifest['resources/css/style.css']['file'];

        return <<<HTML
        <link rel="stylesheet" href="{$cssFile}">
        <script type="module" src="{$jsFile}"></script>
        HTML;
    }
}
