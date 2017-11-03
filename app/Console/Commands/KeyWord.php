<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Controllers\KeyWordController;

class KeyWord extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'keyword {opration} {--q=*} {--area=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(KeyWordController $keyword)
    {
        parent::__construct();
        $this->keyword = $keyword;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if($this->argument('opration')=='sug'){
            $q = $this->option('q');
            $area = $this->option('area');
      
      			if(!$area){
      				$area = ['wireless'];
      		  }
            if($q){
            	$this->keyword->sug($q,$area);
            }else{
            	$this->keyword->sug();
            }       	

        }
    }
}
