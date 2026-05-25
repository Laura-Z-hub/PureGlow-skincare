<?php
declare(strict_types=1);

function collectProductImages(array $files, string $imageUrl, array $existingImages = []): array
{
    $images = [];
    $imageUrl = trim($imageUrl);

    foreach (uploadProductImages($files) as $uploadedImage) {
        $images[] = $uploadedImage;
    }

    if ($imageUrl !== '') {
        $images[] = $imageUrl;
    }

    if (empty($images)) {
        $images = $existingImages;
    }

    return array_values(array_filter($images, fn($image) => is_string($image) && trim($image) !== ''));
}

function productImageAdminSrc(string $image): string
{
    $image = trim($image);

    if ($image === '' || str_starts_with($image, 'http://') || str_starts_with($image, 'https://') || str_starts_with($image, '/')) {
        return $image;
    }

    return '../' . $image;
}

function uploadProductImages(array $files): array
{
    if (empty($files['product_images']) || !is_array($files['product_images']['name'])) {
        return [];
    }

    $uploaded = [];
    $uploadDir = dirname(__DIR__) . '/uploads/products';
    $publicDir = 'uploads/products';
    $allowedMimeTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $fileCount = count($files['product_images']['name']);

    for ($index = 0; $index < $fileCount; $index++) {
        $error = (int)$files['product_images']['error'][$index];

        if ($error === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        if ($error !== UPLOAD_ERR_OK) {
            throw new RuntimeException('One of the product images could not be uploaded.');
        }

        $tmpName = $files['product_images']['tmp_name'][$index];
        $size = (int)$files['product_images']['size'][$index];

        if ($size > 5 * 1024 * 1024) {
            throw new RuntimeException('Product images must be 5MB or smaller.');
        }

        $mimeType = $finfo->file($tmpName) ?: '';

        if (!isset($allowedMimeTypes[$mimeType])) {
            throw new RuntimeException('Only JPG, PNG, WebP, or GIF product images are allowed.');
        }

        $filename = sprintf('%s.%s', bin2hex(random_bytes(16)), $allowedMimeTypes[$mimeType]);
        $destination = $uploadDir . '/' . $filename;

        if (!move_uploaded_file($tmpName, $destination)) {
            throw new RuntimeException('Could not save the uploaded product image.');
        }

        $uploaded[] = $publicDir . '/' . $filename;
    }

    return $uploaded;
}
