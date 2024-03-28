<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        if (0 < User::query()->count()) {
            echo "Skipped, 'users' table is not empty\n";

            return;
        }

        DB::table('users')->delete();

        User::query()->create(
            [
                'id' => 1,
                'role_id' => 1,
                'carrier_id' => 0,
                'password' => '$2y$10$MqTKvc51cdpxIQznIhtuwOaBOW2V2cjFuo5nBAFeY.ZDIFqId4iye',
                'created_at' => '2015-03-15 12:00:00',
                'updated_at' => '2015-03-15 12:00:00',
                'deleted_at' => null,
            ]
        );
        User::query()->create(
            [
                'id' => 2,
                'role_id' => 1,
                'carrier_id' => 0,
                'password' => '$2y$10$MqTKvc51cdpxIQznIhtuwOaBOW2V2cjFuo5nBAFeY.ZDIFqId4iye',
                'created_at' => '2015-03-15 12:00:00',
                'updated_at' => '2015-03-15 12:00:00',
                'deleted_at' => null,
            ]
        );
        User::query()->create(
            [
                'id' => 3,
                'role_id' => 1,
                'carrier_id' => 0,
                'password' => '$2y$10$MqTKvc51cdpxIQznIhtuwOaBOW2V2cjFuo5nBAFeY.ZDIFqId4iye',
                'created_at' => '2015-03-15 12:00:00',
                'updated_at' => '2015-03-15 12:00:00',
                'deleted_at' => null,
            ]
        );

        User::query()->create(
            [
                'id' => 4,
                'role_id' => 1,
                'carrier_id' => 0,
                'password' => '$2y$10$MqTKvc51cdpxIQznIhtuwOaBOW2V2cjFuo5nBAFeY.ZDIFqId4iye',
                'created_at' => '2015-03-15 12:00:00',
                'updated_at' => '2015-03-15 12:00:00',
                'deleted_at' => null,
            ]
        );
    }
}
