<?php
class FileUploader
{
    private $s3Client;
    private $bucketName;

    public function __construct($s3Client, $bucketName)
    {
        $this->s3Client = $s3Client;
        $this->bucketName = $bucketName;
    }

    public function uploadFile($file)
    {
        $fileName = uniqid() . '-' . $file['name'];

        try {
            // Upload the file to S3 bucket
            $result = $this->s3Client->putObject([
                'Bucket' => $this->bucketName,
                'Key' => $fileName,
                'SourceFile' => $file['tmp_name'],
            ]);

            // Get the URL of the uploaded file
            $fileUrl = $result['ObjectURL'];

            return $fileUrl;
        } catch (AwsException $e) {
            // Handle the exception or log an error message
            return null;
        }
    }
}
