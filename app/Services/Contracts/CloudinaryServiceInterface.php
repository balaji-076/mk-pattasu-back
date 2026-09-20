<?php

namespace App\Services\Contracts;

use Illuminate\Http\UploadedFile;

interface CloudinaryServiceInterface
{
    /**
     * @return array{url: string, public_id: string}|null
     */
    public function uploadProductImage(UploadedFile $file, string $category): ?array;

    public function deleteProductImage(string $publicId): void;
}