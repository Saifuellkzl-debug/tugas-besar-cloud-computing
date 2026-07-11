<?php

namespace App\Controllers;

use App\Models\Material;
use App\Helpers\S3Helper;

class MaterialController extends Controller {
    /**
     * Display a listing of materials with search/filter features
     */
    public function index(): void {
        $search = $_GET['search'] ?? '';
        $course = $_GET['course'] ?? '';

        $materials = Material::all($search, $course);
        $courses = Material::getCourses();

        // Attach pre-signed URLs for direct viewing in-browser
        foreach ($materials as &$m) {
            $m['view_url'] = S3Helper::getPresignedUrl($m['s3_key']);
        }

        $this->render('materials/index', [
            'materials'       => $materials,
            'courses'         => $courses,
            'search'          => $search,
            'selected_course' => $course,
            'active_page'     => 'materials'
        ]);
    }

    /**
     * Display the create material form
     */
    public function create(): void {
        $courses = Material::getCourses();
        $this->render('materials/create', [
            'courses'     => $courses,
            'active_page' => 'upload'
        ]);
    }

    /**
     * Process material uploads and metadata storage
     */
    public function store(): void {
        $title = trim($_POST['title'] ?? '');
        $course = trim($_POST['course'] ?? '');
        $lecturer = trim($_POST['lecturer'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($title === '' || $course === '' || $lecturer === '') {
            $_SESSION['flash_error'] = 'Judul, Mata Kuliah, dan Dosen wajib diisi!';
            $this->redirect('/upload');
        }

        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash_error'] = 'File wajib diunggah atau terjadi kesalahan saat upload.';
            $this->redirect('/upload');
        }

        $file = $_FILES['file'];
        
        // 50 MB limit
        if ($file['size'] > 50 * 1024 * 1024) {
            $_SESSION['flash_error'] = 'Ukuran file maksimal adalah 50MB!';
            $this->redirect('/upload');
        }

        $originalName = basename($file['name']);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $safeName = preg_replace('/[^a-zA-Z0-9\._-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
        $s3Key = 'materials/' . uniqid() . '_' . $safeName . '.' . $extension;

        // Upload file to MinIO
        $uploadResult = S3Helper::uploadFile($file['tmp_name'], $s3Key, $file['type']);

        if (!$uploadResult['success']) {
            $_SESSION['flash_error'] = 'Gagal mengunggah ke S3: ' . $uploadResult['message'];
            $this->redirect('/upload');
        }

        // Save metadata details to Database
        $saved = Material::create([
            'title'       => $title,
            'course'      => $course,
            'lecturer'    => $lecturer,
            'description' => $description,
            'file_name'   => $originalName,
            's3_key'      => $s3Key,
            'file_size'   => $file['size'],
            'file_type'   => $file['type']
        ]);

        if ($saved) {
            $_SESSION['flash_success'] = 'Materi kuliah berhasil diunggah!';
            $this->redirect('/materials');
        } else {
            // Rollback uploaded file from S3 if DB storage fails
            S3Helper::deleteFile($s3Key);
            $_SESSION['flash_error'] = 'Gagal menyimpan metadata materi ke database.';
            $this->redirect('/upload');
        }
    }

    /**
     * Download a material using a secure pre-signed S3 download URL
     */
    public function download(): void {
        $id = (int) ($_GET['id'] ?? 0);
        $material = Material::find($id);

        if (!$material) {
            $_SESSION['flash_error'] = 'Materi tidak ditemukan.';
            $this->redirect('/materials');
        }

        // Request a pre-signed S3 URL that enforces attachment headers with the original filename
        $url = S3Helper::getPresignedUrl($material['s3_key'], $material['file_name']);
        
        if ($url !== '') {
            $this->redirect($url);
        } else {
            $_SESSION['flash_error'] = 'Gagal membuat link download.';
            $this->redirect('/materials');
        }
    }

    /**
     * Remove material from storage and database
     */
    public function destroy(): void {
        $id = (int) ($_POST['id'] ?? 0);
        $material = Material::find($id);

        if (!$material) {
            $_SESSION['flash_error'] = 'Materi tidak ditemukan.';
            $this->redirect('/materials');
        }

        // Delete the S3 Object
        S3Helper::deleteFile($material['s3_key']);

        // Delete the SQL Record
        $deletedDb = Material::delete($id);

        if ($deletedDb) {
            $_SESSION['flash_success'] = 'Materi berhasil dihapus.';
        } else {
            $_SESSION['flash_error'] = 'Gagal menghapus materi dari database.';
        }

        $this->redirect('/materials');
    }
}
