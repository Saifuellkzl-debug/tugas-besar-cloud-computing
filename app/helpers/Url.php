<?php

namespace App\Helpers;

class Url {
    /**
     * Generate a fully routed URL for any path in the application.
     * Uses query parameters (e.g. index.php?r=/path) to work seamlessly
     * across different servers and subdirectories without configuration.
     */
    public static function to(string $path, array $params = []): string {
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        $scriptDir = rtrim($scriptDir, '/');

        // Build the query parameter URL pointing to the front controller
        $url = $scriptDir . '/index.php?r=' . $path;

        if (!empty($params)) {
            $url .= '&' . http_build_query($params);
        }

        return $url;
    }
}
