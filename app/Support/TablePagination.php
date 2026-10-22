<?php

namespace App\Support;

class TablePagination
{
    public static function perPage(): int
    {
        return max(1, (int) config('maintenance.table_per_page', 10));
    }
}
