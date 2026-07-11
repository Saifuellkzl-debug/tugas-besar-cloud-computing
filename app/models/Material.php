<?php

namespace App\Models;

use App\Helpers\Database;
use PDO;

class Material {
    private static function db(): PDO {
        return Database::getConnection();
    }

    /**
     * Get all materials with optional search and course filters
     */
    public static function all(string $search = '', string $course = ''): array {
        $db = self::db();
        $sql = "SELECT * FROM materials WHERE 1=1";
        $params = [];

        if ($search !== '') {
            $sql .= " AND (title LIKE :search OR lecturer LIKE :search OR file_name LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        if ($course !== '') {
            $sql .= " AND course = :course";
            $params[':course'] = $course;
        }

        $sql .= " ORDER BY uploaded_at DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Find a material by its ID
     */
    public static function find(int $id): ?array {
        $db = self::db();
        $stmt = $db->prepare("SELECT * FROM materials WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Create a new material record
     */
    public static function create(array $data): bool {
        $db = self::db();
        $stmt = $db->prepare("INSERT INTO materials (title, course, lecturer, description, file_name, s3_key, file_size, file_type) 
            VALUES (:title, :course, :lecturer, :description, :file_name, :s3_key, :file_size, :file_type)");
        
        return $stmt->execute([
            ':title'       => $data['title'],
            ':course'      => $data['course'],
            ':lecturer'    => $data['lecturer'],
            ':description' => $data['description'] ?? null,
            ':file_name'   => $data['file_name'],
            ':s3_key'      => $data['s3_key'],
            ':file_size'   => (int) $data['file_size'],
            ':file_type'   => $data['file_type'],
        ]);
    }

    /**
     * Delete a material record by ID
     */
    public static function delete(int $id): bool {
        $db = self::db();
        $stmt = $db->prepare("DELETE FROM materials WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Calculate summary statistics for the dashboard
     */
    public static function getStats(): array {
        $db = self::db();
        
        $totalFiles = $db->query("SELECT COUNT(*) FROM materials")->fetchColumn();
        $totalSize = $db->query("SELECT SUM(file_size) FROM materials")->fetchColumn() ?: 0;
        $totalCourses = $db->query("SELECT COUNT(DISTINCT course) FROM materials")->fetchColumn();
        $totalLecturers = $db->query("SELECT COUNT(DISTINCT lecturer) FROM materials")->fetchColumn();

        return [
            'total_files' => (int) $totalFiles,
            'total_size'  => (int) $totalSize,
            'total_courses' => (int) $totalCourses,
            'total_lecturers' => (int) $totalLecturers
        ];
    }

    /**
     * Get recent uploads
     */
    public static function getRecent(int $limit = 5): array {
        $db = self::db();
        $stmt = $db->prepare("SELECT * FROM materials ORDER BY uploaded_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get all unique course names
     */
    public static function getCourses(): array {
        $db = self::db();
        return $db->query("SELECT DISTINCT course FROM materials ORDER BY course ASC")->fetchAll(PDO::FETCH_COLUMN);
    }
}
