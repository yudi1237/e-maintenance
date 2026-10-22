<?php

namespace App\Support;

use App\Models\PreventiveChecklistItem;

class PreventiveConveyorChecklist
{
    /**
     * @return list<array{key: string, label: string, items: array<string, array{label: string, standard: string}>}>
     */
    public static function groups(): array
    {
        $fromDb = PreventiveChecklistItem::groupsForCategory('CONVEYOR');

        return $fromDb !== [] ? $fromDb : self::builtinGroups();
    }

    /**
     * @return list<array{key: string, label: string, items: array<string, array{label: string, standard: string}>}>
     */
    public static function builtinGroups(): array
    {
        return [
            [
                'key' => 'belt',
                'label' => 'Belt (Sabuk)',
                'items' => [
                    'belt_physical' => [
                        'label' => 'Kondisi Fisik Belt',
                        'standard' => 'Kondisi Belt tidak retak/sobek',
                    ],
                    'belt_tension' => [
                        'label' => 'Cek Ketegangan & Kelurusan Belt',
                        'standard' => 'Belt tidak longgar dan lurus',
                    ],
                ],
            ],
            [
                'key' => 'drive',
                'label' => 'Unit Penggerak',
                'items' => [
                    'drive_motor' => [
                        'label' => 'Cek kondisi motor & rantai sprocket',
                        'standard' => 'Suhu & suara motor halus',
                    ],
                    'drive_roller' => [
                        'label' => 'Cek Kondisi Roller & Bearing',
                        'standard' => 'Putaran drive & tail pulley halus, bersih',
                    ],
                    'drive_greasing' => [
                        'label' => 'Pelumasan Block Bearing',
                        'standard' => 'Lakukan pelumasan pada block bearing',
                    ],
                ],
            ],
            [
                'key' => 'electrical',
                'label' => 'Kelistrikan',
                'items' => [
                    'electrical_cabling' => [
                        'label' => 'Cek Kondisi Perkabelan Conveyor',
                        'standard' => 'Kabel tidak semrawut, tidak terkena air, rapi',
                    ],
                    'electrical_inverter' => [
                        'label' => 'Cek fungsi Inverter/ VFD',
                        'standard' => 'Respon pengatur kecepatan sesuai',
                    ],
                    'electrical_safety' => [
                        'label' => 'Fungsi tombol E-Stop & Safety lainnya',
                        'standard' => 'Cek fungsi',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function items(): array
    {
        $items = [];

        foreach (self::groups() as $group) {
            foreach ($group['items'] as $key => $item) {
                $items[$key] = $item['label'];
            }
        }

        return $items;
    }

    /**
     * @return array<string, string>
     */
    public static function valueLabels(): array
    {
        return [
            PreventiveChecklist::OK => 'OK',
            PreventiveChecklist::NOT_OK => 'Not OK',
        ];
    }

    /**
     * @param  array<string, string>  $submitted
     */
    public static function result(array $submitted): string
    {
        foreach (array_keys(self::items()) as $key) {
            if (($submitted[$key] ?? null) === PreventiveChecklist::NOT_OK) {
                return 'follow_up';
            }
        }

        return 'ok';
    }
}
