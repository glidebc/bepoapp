<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Live;
use Log;

class LiveConsole extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature='bepo.liveworker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description='bepo live worker';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $lives=Live::where('enabled','=','1')->get();
        foreach($lives as $data){
            $this->go($data);
        }
    }

    public function go($data){
        $msg=json_encode($data->toArray());
        Log::debug(__CLASS__.' '.$msg);
    }
}
