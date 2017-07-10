<?php

use Illuminate\Database\Seeder;

class SourcesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sources')->insert([
            'name' => str_random(12),
            'url' => 'http://'.str_random(12),
            'has_license' => true,
        ]
		);
    }
}
