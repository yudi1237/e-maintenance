<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PreventiveEvidence
{
    public const MAX_FILES = 10;

    public const MAX_KILOBYTES = 5120;

    public const DIRECTORY = 'preventive-evidence';

    public const CONVEYOR_DIRECTORY = 'preventive-conveyor-evidence';

    /** @return list<string> */
    public static function mimeTypes(): array
    {
        return ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    }

    /** @return array<string, list<mixed>> */
    public static function rules(): array
    {
        return [
            'evidences' => ['nullable', 'array', 'max:'.self::MAX_FILES],
            'evidences.*' => [
                'file',
                'max:'.self::MAX_KILOBYTES,
                'mimes:'.implode(',', self::mimeTypes()),
            ],
        ];
    }

    /** @return list<string> */
    public static function storeFromRequest(Request $request, string $directory = self::DIRECTORY): array
    {
        $files = $request->file('evidences', []);
        if (! is_array($files)) {
            $files = $files ? [$files] : [];
        }

        $paths = [];
        foreach ($files as $file) {
            if (! $file instanceof UploadedFile || ! $file->isValid()) {
                continue;
            }

            $paths[] = $file->store($directory, 'public');
        }

        return $paths;
    }

    /**
     * @param  list<string>|null  $paths
     * @return list<string>
     */
    public static function urls(?array $paths): array
    {
        return collect($paths ?? [])
            ->filter(fn ($path): bool => is_string($path) && $path !== '')
            ->map(fn (string $path): string => asset('storage/'.$path))
            ->values()
            ->all();
    }

    /**
     * Gambar evidence sebagai data URI agar bisa masuk ke PDF tanpa memuat file dari internet.
     *
     * @param  list<string>|null  $paths
     * @return list<string>
     */
    public static function pdfDataUris(?array $paths): array
    {
        $uris = [];

        foreach ($paths ?? [] as $path) {
            if (! is_string($path) || $path === '') {
                continue;
            }

            $absolute = Storage::disk('public')->path($path);
            if (! is_file($absolute)) {
                continue;
            }

            $uri = self::imageDataUri($absolute);
            if ($uri !== null) {
                $uris[] = $uri;
            }
        }

        return $uris;
    }

    /** @param  list<string>|null  $paths */
    public static function delete(?array $paths): void
    {
        foreach ($paths ?? [] as $path) {
            if (is_string($path) && $path !== '') {
                Storage::disk('public')->delete($path);
            }
        }
    }

    private static function imageDataUri(string $absolute): ?string
    {
        $contents = file_get_contents($absolute);
        if ($contents === false || $contents === '') {
            return null;
        }

        $mime = mime_content_type($absolute) ?: '';
        if (! function_exists('imagecreatefromstring')) {
            return in_array($mime, ['image/jpeg', 'image/png', 'image/gif'], true)
                ? 'data:'.$mime.';base64,'.base64_encode($contents)
                : null;
        }

        $image = @imagecreatefromstring($contents);
        if ($image === false) {
            return in_array($mime, ['image/jpeg', 'image/png', 'image/gif'], true)
                ? 'data:'.$mime.';base64,'.base64_encode($contents)
                : null;
        }

        $width = imagesx($image);
        $height = imagesy($image);
        $max = 480;
        if ($width > $max || $height > $max) {
            $scale = $max / max($width, $height);
            $scaled = imagescale($image, max(1, (int) round($width * $scale)), max(1, (int) round($height * $scale)));
            if ($scaled !== false) {
                imagedestroy($image);
                $image = $scaled;
            }
        }

        ob_start();
        imagejpeg($image, null, 75);
        $jpeg = ob_get_clean();
        imagedestroy($image);

        return is_string($jpeg) && $jpeg !== ''
            ? 'data:image/jpeg;base64,'.base64_encode($jpeg)
            : null;
    }
}
