<?php

namespace App\Controllers;

class Controller {
    /**
     * Render a view wrapped in the master layout
     */
    protected function render(string $view, array $data = []): void {
        extract($data);
        
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        
        if (!file_exists($viewFile)) {
            die("View file '$view' not found at expected path: " . $viewFile);
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Load the main layout file which includes the header, footer and wraps the $content variable.
        require __DIR__ . '/../views/layout.php';
    }

    protected function redirect(string $url): void {
        if (str_starts_with($url, '/')) {
            $url = \App\Helpers\Url::to($url);
        }
        header("Location: " . $url);
        exit;
    }

    /**
     * Respond with JSON data
     */
    protected function json($data, int $status = 200): void {
        header("Content-Type: application/json");
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
}
