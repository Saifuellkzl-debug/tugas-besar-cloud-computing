<?php

namespace App\Controllers;

use App\Models\Material;
use App\Helpers\S3Helper;

class HomeController extends Controller {
    /**
     * Display the dashboard page
     */
    public function index(): void {
        $stats = Material::getStats();
        $recentMaterials = Material::getRecent(5);

        // Generate pre-signed viewing URLs for the recent files
        foreach ($recentMaterials as &$m) {
            $m['view_url'] = S3Helper::getPresignedUrl($m['s3_key']);
        }

        $this->render('dashboard', [
            'stats' => $stats,
            'recent' => $recentMaterials,
            'active_page' => 'dashboard'
        ]);
    }
}
