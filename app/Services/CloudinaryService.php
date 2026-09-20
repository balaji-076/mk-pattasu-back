<?php

namespace App\Services;

use App\Services\Contracts\CloudinaryServiceInterface;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CloudinaryService implements CloudinaryServiceInterface
{
    public function __construct()
    {
        Configuration::instance(config('services.cloudinary.url'));
    }

    /**
     * @return array{url: string, public_id: string}|null
     */
    public function uploadProductImage(UploadedFile $file, string $category): ?array
    {
        try {
            $folder       = 'products/' . Str::slug($category, '_');
            $uploadResult = (new UploadApi())->upload(
                $file->getRealPath(),
                [
                    'folder'       => $folder,
                    'format'       => 'webp',
                    'quality'      => 'auto',
                    'fetch_format' => 'auto',
                ]
            );

            if (empty($uploadResult['secure_url']) || empty($uploadResult['public_id'])) {
                Log::error('Cloudinary upload response missing required keys', [
                    'response' => $uploadResult,
                ]);

                return null;
            }

            return [
                'url'       => $uploadResult['secure_url'],
                'public_id' => $uploadResult['public_id'],
            ];
        } catch (\Throwable $e) {
            Log::error('Cloudinary uploadProductImage failed', [
                'category'  => $category, 'file_name' => $file->getClientOriginalName(),
                'error'     => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function deleteProductImage(string $publicId): void
    {
        try {
            $response = (new UploadApi())->destroy($publicId);
            Log::info('Cloudinary product image deleted', [
                'public_id' => $publicId,
                'response'  => $response->getArrayCopy(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Cloudinary deleteProductImage failed', [
                'public_id' => $publicId, 'error'     => $e->getMessage(),
            ]);
        }
    }
}