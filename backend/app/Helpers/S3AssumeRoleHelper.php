<?php

namespace App\Helpers;

use Aws\S3\S3Client;
use Aws\Sts\StsClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class S3AssumeRoleHelper
{
    /**
     * Create a temporary S3 client using AWS STS AssumeRole
     */
    protected static function getS3Client(string $roleArn, string $region = null): S3Client
    {
        $region = $region ?? env('AWS_STS_DEFAULT_REGION', 'ap-south-1');

        try {
            $stsClient = new StsClient([
                'version'     => 'latest',
                'region'      => $region,
                'credentials' => [
                    'key'    => env('AWS_STS_ACCESS_KEY_ID'),
                    'secret' => env('AWS_STS_SECRET_ACCESS_KEY'),
                ],
            ]);

            $assumedRole = $stsClient->assumeRole([
                'RoleArn'         => $roleArn,
                'RoleSessionName' => 'CMSS3Session_' . uniqid(),
                'DurationSeconds' => 3600, // 1 hour session
            ]);

            $creds = $assumedRole['Credentials'];

            return new S3Client([
                'version'     => 'latest',
                'region'      => $region,
                'credentials' => [
                    'key'    => $creds['AccessKeyId'],
                    'secret' => $creds['SecretAccessKey'],
                    'token'  => $creds['SessionToken'],
                ],
            ]);

        } catch (\Throwable $e) {
            Log::error('❌ S3AssumeRoleHelper: Failed to assume role', [
                'roleArn' => $roleArn,
                'error'   => $e->getMessage(),
            ]);
            throw new \RuntimeException('Failed to assume role: ' . $e->getMessage());
        }
    }

    /**
     * Sanitize and create globally unique filename
     */
    protected static function generateSafeFileName(string $originalName): string
    {
        // Remove special characters, normalize spaces and dots
        $clean = preg_replace('/[^A-Za-z0-9\._-]/', '_', $originalName);
        $clean = trim($clean, '_');

        // Limit length to avoid S3 key overflow (max ~1024 chars)
        $clean = Str::limit($clean, 200, '');

        return Str::uuid() . '_' . $clean;
    }

    /**
     * Upload file (handles UploadedFile, string path, or raw content)
     */
    public static function uploadFile(
        string $roleArn,
        string $bucket,
        $file,
        string $pathPrefix = '',
        string $acl = 'private',
        bool $returnPublicUrl = false,
        string $region = null
    ): array {
                
        try {
            $s3 = self::getS3Client($roleArn, $region);                        
            $region = $region ?? env('AWS_DEFAULT_REGION', 'ap-south-1');

            // Determine filename
            if ($file instanceof UploadedFile) {
                $originalName = $file->getClientOriginalName();
                $mimeType = $file->getMimeType();
                $fileStream = fopen($file->getRealPath(), 'r');
            } elseif (is_string($file) && file_exists($file)) {
                $originalName = basename($file);
                $mimeType = mime_content_type($file) ?: 'application/octet-stream';
                $fileStream = fopen($file, 'r');
            } else {
                $originalName = 'raw_content.txt';
                $mimeType = 'text/plain';
                $fileStream = $file; // direct string content
            }

            $safeFileName = self::generateSafeFileName($originalName);
            $key = trim($pathPrefix, '/') . '/' . $safeFileName;

            // Perform upload
            $s3->putObject([
                'Bucket'      => $bucket,
                'Key'         => $key,
                'Body'        => $fileStream,                
                'ContentType' => $mimeType,
            ]);

            $fileUrl = $returnPublicUrl
                ? self::getPublicUrl($bucket, $key, $region)
                : null;

            Log::info('✅ File uploaded successfully to S3', [
                'bucket' => $bucket,
                'key'    => $key,
                'url'    => $fileUrl,
            ]);

            return [
                'success' => true,
                'bucket'  => $bucket,
                'key'     => $key,
                'url'     => $fileUrl,
            ];

        } catch (\Throwable $e) {
            Log::error('❌ S3AssumeRoleHelper: Upload failed', [
                'error' => $e->getMessage(),
                'file'  => $file instanceof UploadedFile ? $file->getClientOriginalName() : '[raw|string]',
            ]);

            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }

    /**
     * Read file contents from S3
     */
    public static function readFile(string $roleArn, string $bucket, string $key, string $region = null): ?string
    {
        try {
            $s3 = self::getS3Client($roleArn, $region);            
            $result = $s3->getObject(['Bucket' => $bucket, 'Key' => $key]);            
            return (string) $result['Body'];
        } catch (\Throwable $e) {
            Log::error('❌ S3AssumeRoleHelper: Read failed', [
                'bucket' => $bucket,
                'key'    => $key,
                'error'  => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Delete file from S3
     */
    public static function deleteFile(string $roleArn, string $bucket, string $key, string $region = null): bool
    {
        try {
            $s3 = self::getS3Client($roleArn, $region);
            $s3->deleteObject(['Bucket' => $bucket, 'Key' => $key]);
            Log::info('🗑️ File deleted from S3', compact('bucket', 'key'));
            return true;
        } catch (\Throwable $e) {
            Log::warning('⚠️ Failed to delete file from S3', [
                'bucket' => $bucket,
                'key'    => $key,
                'error'  => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Generate public S3 URL (or CloudFront if configured)
     */
    protected static function getPublicUrl(string $bucket, string $key, string $region): string
    {
        $cdnDomain = env('AWS_CLOUDFRONT_URL'); // e.g. d123.cloudfront.net
        if ($cdnDomain) {
            return rtrim($cdnDomain, '/') . '/' . ltrim($key, '/');
        }

        return "https://{$bucket}.s3.{$region}.amazonaws.com/{$key}";
    }
}
