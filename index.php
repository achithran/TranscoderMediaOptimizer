<?php
require 'vendor/autoload.php';
require 'FileUploader.php';
require 'Transcoder.php';
require 'CompressionManager.php';

use Aws\Credentials\Credentials;
use Aws\S3\S3Client;
use Aws\ElasticTranscoder\ElasticTranscoderClient;

// Set your AWS credentials
$accessKeyId = 'AKIA22QVLEX4QO4TP7K6';
$secretAccessKey = 'rWYmpe+vSb7bF7pomYlQ2mTh0vD9JyBTMxuJfTt+';
$bucketName = 'transcodermediaoptimize';
$region  = 'us-east-1';


$credentials = new Credentials($accessKeyId, $secretAccessKey);

// Instantiate the S3 client
$s3Client = new S3Client([
    'version' => 'latest',
    'region' => $region,
    'credentials' => [
        'key' => $accessKeyId,
        'secret' => $secretAccessKey
    ]
]);

// Check if a file was uploaded
if (isset($_FILES['file'])) {
    $file = $_FILES['file'];

    // Create an instance of the FileUploader
    $fileUploader = new FileUploader($s3Client, $bucketName);

    // Upload the file to S3 bucket
    $uploadedFileUrl = $fileUploader->uploadFile($file);

    if ($uploadedFileUrl) {
        echo 'File uploaded successfully. URL: <a href="' . $uploadedFileUrl . '">' . $uploadedFileUrl . '</a>';
        echo "<br>";

        // Create an instance of the Transcoder
        $transcoder = new Transcoder(new ElasticTranscoderClient([
            'version' => '2012-09-25',
            'region' => $region,
            'credentials' => $credentials
        ]));

        // Start transcoding
        $transcodedFiles = $transcoder->transcodeFile($file);

        // Create an instance of the CompressionManager
        $compressionManager = new CompressionManager($s3Client, $bucketName);

        // Apply compression logic for each transcoded file
        foreach ($transcodedFiles as $transcodedFile) {
            $compressedFileUrl = $compressionManager->applyCompressionLogic($transcodedFile);

            if ($compressedFileUrl) {
                echo "Compressed File URL: " . $compressedFileUrl . "<br>";
            } else {
                echo "Compression failed for file: " . $transcodedFile . "<br>";
            }
        }
    } else {
        echo 'Error uploading file.';
    }
} else {
    // No file uploaded
    echo 'Please choose a file to upload.';
}
