<?php

return [
    'machine_count' => (int) env('MAINTENANCE_MACHINE_COUNT', 60),

    'table_per_page' => (int) env('MAINTENANCE_TABLE_PER_PAGE', 10),

    'sla_target_minutes' => (int) env('MAINTENANCE_SLA_TARGET_MINUTES', 60),
];
