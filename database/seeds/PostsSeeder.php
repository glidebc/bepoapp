<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PostsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i=0;$i<1000;$i++)
        DB::table('posts')->insert([
            'title'=>str_random(10),
            'content'=>str_random(1000),
            'status'=>1,
            'views'=>9999,
            'author_id'=>1,
            'updated_at'=>CarBon::today(),
            'created_at'=>CarBon::today(),
        ]);
    }
}
