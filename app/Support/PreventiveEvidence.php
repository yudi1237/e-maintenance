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

    /** @param  list<string>|null  $paths */
    public static function delete(?array $paths): void
    {
        foreach ($paths ?? [] as $path) {
            if (is_string($path) && $path !== '') {
                Storage::disk('public')->delete($path);
            }
        }
    }
}
