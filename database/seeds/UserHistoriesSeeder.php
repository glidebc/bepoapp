<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UserHistoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i=0;$i<1000;$i++){
            $logtypes=config('global.logtypes');
            $max=count($logtypes);
            $idx=rand(0,$max-1);
            DB::table('user_histories')->insert([
                'log' => str_random(100),
                'category'=>$logtypes[$idx],
                'updated_at'=>CarBon::today(),
                'created_at'=>CarBon::today(),
            ]);
        }
    }
}
