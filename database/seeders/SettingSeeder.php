<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'saw_c1', 'value' => '0.30'],
            ['key' => 'saw_c2', 'value' => '0.20'],
            ['key' => 'saw_c3', 'value' => '0.20'],
            ['key' => 'saw_c4', 'value' => '0.30'],
        ];

        foreach ($settings as $setting) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }
}
