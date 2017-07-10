<?php

use Illuminate\Database\Seeder;
use App\System;

class SystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		System::create(
			[
            'name' => str_random(12),
            'description' => str_random(100),
            'path' => '/'.str_random(12),
            'priority' => 1,
            'enabled' => true,
        ]
		);
    }
}
