<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Exception;
use InvalidArgumentException;

class FileUploadService
{
    private array $allowedImageTypes = ['jpeg', 'jpg', 'png', 'webp'];
    private int $maxFileSize = 2048; // KB
    private int $maxWidth = 1200;
    private int $maxHeight = 800;
    private int $webpQuality = 85;

    /**
     * Upload and optimize an image file
     *
     * @param UploadedFile $file
     * @param string $directory
     * @return string The path to the uploaded file
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function uploadImage(UploadedFile $file, string $directory = 'uploads'): string
    {
        try {
            // Validate the uploaded file
            $this->validateFile($file);

            // Generate secure filename
            $filename = $this->generateSecureFilename($file);

            // Process and save the image
            return $this->processAndSaveImage($file, $directory, $filename);

        } catch (Exception $e) {
            // Log the error for debugging
            \Log::error('File upload failed: ' . $e->getMessage(), [
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'directory' => $directory
            ]);

            throw $e;
        }
    }

    /**
     * Delete a file from storage
     *
     * @param string $path
     * @return bool
     */
    public function deleteFile(string $path): bool
    {
        try {
            if (Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->delete($path);
            }
            return false;
        } catch (Exception $e) {
            \Log::error('File deletion failed: ' . $e->getMessage(), ['path' => $path]);
            return false;
        }
    }

    /**
     * Validate the uploaded file
     *
     * @param UploadedFile $file
     * @throws InvalidArgumentException
     */
    private function validateFile(UploadedFile $file): void
    {
        // Check if file is valid
        if (!$file->isValid()) {
            throw new InvalidArgumentException('Invalid file upload.');
        }

        // Validate file type by extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $this->allowedImageTypes)) {
            throw new InvalidArgumentException('Invalid file type. Only JPEG, JPG, PNG, and WebP are allowed.');
        }

        // Validate MIME type for extra security
        $mimeType = $file->getMimeType();
        $allowedMimeTypes = [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/webp'
        ];

        if (!in_array($mimeType, $allowedMimeTypes)) {
            throw new InvalidArgumentException('Invalid file MIME type.');
        }

        // Validate file size
        if ($file->getSize() > $this->maxFileSize * 1024) {
            throw new InvalidArgumentException("File size too large. Maximum {$this->maxFileSize}KB allowed.");
        }

        // Additional security: Check if file is actually an image
        try {
            $imageInfo = getimagesize($file->getRealPath());
            if ($imageInfo === false) {
                throw new InvalidArgumentException('File is not a valid image.');
            }
        } catch (Exception $e) {
            throw new InvalidArgumentException('Unable to process image file.');
        }
    }

    /**
     * Generate a secure filename
     *
     * @param UploadedFile $file
     * @return string
     */
    private function generateSecureFilename(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        return Str::random(40) . '.' . $extension;
    }

    /**
     * Process and save the image
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string $filename
     * @return string
     * @throws Exception
     */
    private function processAndSaveImage(UploadedFile $file, string $directory, string $filename): string
    {
        try {
            // Create image instance
            $image = Image::read($file->getRealPath());

            // Resize if too large
            if ($image->width() > $this->maxWidth || $image->height() > $this->maxHeight) {
                $image->resize($this->maxWidth, $this->maxHeight, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            // Generate WebP path
            $webpFilename = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
            $webpPath = $directory . '/' . $webpFilename;

            // Ensure directory exists
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory, 0755, true);
            }

            // Convert to WebP and save
            $webpContent = $image->toWebp($this->webpQuality);

            if (!Storage::disk('public')->put($webpPath, $webpContent)) {
                throw new Exception('Failed to save the processed image.');
            }

            return $webpPath;

        } catch (Exception $e) {
            throw new Exception('Failed to process image: ' . $e->getMessage());
        }
    }

    /**
     * Get allowed file types
     *
     * @return array
     */
    public function getAllowedTypes(): array
    {
        return $this->allowedImageTypes;
    }

    /**
     * Get maximum file size in KB
     *
     * @return int
     */
    public function getMaxFileSize(): int
    {
        return $this->maxFileSize;
    }

    /**
     * Set maximum file size in KB
     *
     * @param int $size
     * @return self
     */
    public function setMaxFileSize(int $size): self
    {
        $this->maxFileSize = $size;
        return $this;
    }

    /**
     * Set image dimensions
     *
     * @param int $width
     * @param int $height
     * @return self
     */
    public function setMaxDimensions(int $width, int $height): self
    {
        $this->maxWidth = $width;
        $this->maxHeight = $height;
        return $this;
    }
}
