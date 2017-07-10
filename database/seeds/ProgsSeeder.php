<?php

use Illuminate\Database\Seeder;
use app\Progs;

class ProgsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('progs')->insert([
        	'system_id'=>1,
            'name' => str_random(10),
            'path' => '/'.str_random(12),
            'priority' => 1,
            'enabled' => true,
        ]);
    }
}
