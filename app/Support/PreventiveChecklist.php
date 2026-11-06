<?php

namespace App\Support;

class PreventiveChecklist
{
    public const OK = 'ok';

    public const NOT_OK = 'not_ok';

    public const NA = 'na';

    /**
     * @return array<string, string>
     */
    public static function items(): array
    {
        return [
            'hydraulic_oil' => 'Cek level oli hidrolik',
            'oil_leak' => 'Cek kebocoran oli / selang',
            'heater' => 'Cek suhu heater & barrel',
            'safety_door' => 'Cek safety door / interlock',
            'greasing' => 'Cek greasing moving parts',
            'cooling' => 'Cek sistem cooling / water leak',
            'electrical' => 'Cek kelistrikan & panel',
            'cleanliness' => 'Kebersihan area mesin & hopper',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function valueLabels(): array
    {
        return [
            self::OK => 'OK',
            self::NOT_OK => 'Tidak OK',
            self::NA => 'N/A',
        ];
    }

    /**
     * @param  array<string, string>  $submitted
     */
    public static function result(array $submitted): string
    {
        foreach (array_keys(self::items()) as $key) {
            if (($submitted[$key] ?? null) === self::NOT_OK) {
                return 'follow_up';
            }
        }

        return 'ok';
    }

    /**
     * @param  list<string>  $allowedKeys
     * @param  array<string, mixed>  $remarks
     * @return array<string, string>
     */
    public static function sanitizeRemarks(array $allowedKeys, array $remarks): array
    {
        $allowed = array_fill_keys($allowedKeys, true);
        $clean = [];

        foreach ($remarks as $key => $value) {
            $key = (string) $key;
            if (! isset($allowed[$key])) {
                continue;
            }

            $text = trim((string) $value);
            if ($text !== '') {
                $clean[$key] = $text;
            }
        }

        return $clean;
    }
}
