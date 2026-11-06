<?php

namespace App\Support;

class AreaList
{
    /** @return list<string> */
    public static function options(): array
    {
        return [
            'Double Seat',
            'Compounding',
            'Assy SHAD',
            'Assy Helm',
            'Gudang',
            'Crusher',
            'Mixing',
            'Centralisasi Piovan',
            'Painting L1',
            'Painting L2',
            'Painting L3',
            'Lain-lain',
        ];
    }
}
