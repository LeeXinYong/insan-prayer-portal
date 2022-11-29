<?php

namespace Database\Seeders;

use App\Models\SysParam;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SysParamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sys_params = [
            ['key' => 'timeout', 'value' => true],
            ['key' => 'timeout_duration', 'value' => 240],
            ['key' => 'timeout_countdown', 'value' => 60],
            ['key' => 'recaptcha', 'value' => true],
            ['key' => 'recaptcha_max_attempt', 'value' => 1],
        ];

        foreach ($sys_params as $sys_param) {
            SysParam::query()->create($sys_param);
        }
    }
}
