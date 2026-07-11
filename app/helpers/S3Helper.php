<?php

namespace App\Helpers;

use Aws\S3\S3Client;
use Exception;

class S3Helper {
    private static ?S3Client $client = null;
    private static string $bucket = 'materi-perkuliahan';

    public static function getClient(): S3Client {
        if (self::$client === null) {
            $config = require __DIR__ . '/../../config/s3.php';
            self::$client = new S3Client($config);
        }
        return self::$client;
    }

    /**
     * Upload a file to MinIO S3
     */
    public static function uploadFile(string $tempFilePath, string $key, string $mimeType): array {
        $s3 = self::getClient();
        try {
            $result = $s3->putObject([
                'Bucket'      => self::$bucket,
                'Key'         => $key,
                'SourceFile'  => $tempFilePath,
                'ContentType' => $mimeType,
            ]);
            return [
                'success' => true,
                'url'     => $result['ObjectURL'] ?? '',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Delete a file from MinIO S3
     */
    public static function deleteFile(string $key): array {
        $s3 = self::getClient();
        try {
            $s3->deleteObject([
                'Bucket' => self::$bucket,
                'Key'    => $key,
            ]);
            return ['success' => true];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate a pre-signed URL for viewing or downloading a file
     */
    public static function getPresignedUrl(string $key, ?string $originalName = null, string $expires = '+1 hour'): string {
        $s3 = self::getClient();
        try {
            $params = [
                'Bucket' => self::$bucket,
                'Key'    => $key,
            ];

            if ($originalName !== null) {
                $safeName = str_replace('"', '\\"', $originalName);
                $params['ResponseContentDisposition'] = 'attachment; filename="' . $safeName . '"';
            }

            $cmd = $s3->getCommand('GetObject', $params);
            $request = $s3->createPresignedRequest($cmd, $expires);
            return (string) $request->getUri();
        } catch (Exception $e) {
            return '';
        }
    }
}
