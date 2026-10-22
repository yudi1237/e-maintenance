<?php

namespace App\Support;

class PreventivePm2Checklist extends PreventivePm1Checklist
{
    /**
     * @return array<string, array{0: string, 1: string, 2?: array{placeholder: string, suffix: string}}>
     */
    protected static function additionalItems(): array
    {
        return [
            'clean_oil_cooler' => ['Bersihkan Oil Cooler', 'Tidak Berkarak'],
            'replace_oil_tissue_filter' => ['Ganti Filter Tissue Oli', 'Baru'],
            'clean_oil_filter' => ['Bersihkan Filter Oli', 'Bersih'],
            'oil_sample_color' => ['Ambil Sampel, Cek Warna Oli', 'Jernih, Bersih'],
            'check_ring_barrel' => ['Cek Check Ring & Barrel', 'Tidak Bocor'],
        ];
    }
}
