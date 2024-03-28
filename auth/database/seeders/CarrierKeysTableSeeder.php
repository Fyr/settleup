<?php

namespace Database\Seeders;

use App\Models\CarrierKey;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarrierKeysTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        if (0 < CarrierKey::query()->count()) {
            echo "Skipped, 'carrier_keys' table is not empty\n";

            return;
        }

        DB::table('carrier_keys')->delete();

        CarrierKey::query()->create([
            'carrier_id' => 3,
            'key' => 'h78YQEcDj82pCkHS44i9b5fll8hsV0OcmftbptaZeA7lkern011w2ccbRbHiQMqa49hwwrO+7NvWMT0bnKfx4/A==',
        ]);
    }
}
