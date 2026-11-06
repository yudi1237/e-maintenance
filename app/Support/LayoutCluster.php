<?php

namespace App\Support;

use App\Models\LayoutClusterName;

class LayoutCluster
{
    /** @var array<string, string> */
    public const DEFAULTS = [
        'cluster-1' => 'Cluster 1',
        'cluster-2' => 'Cluster 2',
        'cluster-3' => 'Cluster 3',
        'cluster-4' => 'Cluster 4',
        'k1aa' => 'Cluster K1AA',
        'b65' => 'Cluster B65',
        'piece-slide' => 'Cluster PIECE SLIDE',
    ];

    /** @return array<string, string> */
    public static function names(): array
    {
        $stored = LayoutClusterName::query()->pluck('name', 'key');
        $names = [];

        foreach (self::DEFAULTS as $key => $default) {
            $name = trim((string) ($stored[$key] ?? ''));
            $names[$key] = $name !== '' ? $name : $default;
        }

        return $names;
    }
}
