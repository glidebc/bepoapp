<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert([
        	'title'=>str_random(10),
        	'updated_at'=>CarBon::today(),
			'created_at'=>CarBon::today(),
        ]);
    }
}
