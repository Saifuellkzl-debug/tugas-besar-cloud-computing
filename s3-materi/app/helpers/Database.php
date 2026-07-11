<?php

namespace App\Helpers;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../../config/database.php';
            $dbPath = $config['database'];

            $dir = dirname($dbPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            try {
                self::$instance = new PDO("sqlite:" . $dbPath);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                self::initializeSchema(self::$instance);

            } catch (PDOException $e) {
                die("Koneksi database gagal: " . $e->getMessage());
            }
        }

        return self::$instance;
    }

    private static function initializeSchema(PDO $db): void {
        $sql = "CREATE TABLE IF NOT EXISTS materials (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            course TEXT NOT NULL,
            lecturer TEXT NOT NULL,
            description TEXT,
            file_name TEXT NOT NULL,
            s3_key TEXT NOT NULL UNIQUE,
            file_size INTEGER NOT NULL,
            file_type TEXT NOT NULL,
            uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );";

        $db->exec($sql);
    }
}
