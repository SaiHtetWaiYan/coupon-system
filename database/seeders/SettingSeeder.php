<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'site_name', 'value' => 'Coupon System', 'group' => 'general', 'type' => 'string'],
            ['key' => 'site_description', 'value' => 'A Laravel application', 'group' => 'general', 'type' => 'string'],
            ['key' => 'maintenance_mode', 'value' => '0', 'group' => 'general', 'type' => 'boolean'],
            ['key' => 'currency_symbol', 'value' => '$', 'group' => 'general', 'type' => 'string'],
            ['key' => 'logo_path', 'value' => '', 'group' => 'general', 'type' => 'string'],
            ['key' => 'allow_registration', 'value' => '1', 'group' => 'users', 'type' => 'boolean'],
            ['key' => 'require_email_verification', 'value' => '1', 'group' => 'users', 'type' => 'boolean'],
            ['key' => 'restaurant_type', 'value' => 'Restaurant', 'group' => 'coupon', 'type' => 'string'],
            ['key' => 'opening_hours', 'value' => '9:00 AM - 10:00 PM', 'group' => 'coupon', 'type' => 'string'],
            ['key' => 'close_date', 'value' => 'Sunday', 'group' => 'coupon', 'type' => 'string'],
        ];

        foreach ($settings as $setting) {
            Setting::query()->updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
