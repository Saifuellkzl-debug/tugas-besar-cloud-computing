<?php

require '../vendor/autoload.php';

use Aws\S3\S3Client;

$config = require '../config/s3.php';

$s3 = new S3Client($config);

try {

    $result = $s3->listBuckets();

    echo "<h2>Berhasil Terhubung ke MinIO</h2>";

    echo "<hr>";

    foreach ($result['Buckets'] as $bucket) {

        echo $bucket['Name'] . "<br>";
    }
} catch (Exception $e) {

    echo $e->getMessage();
}
