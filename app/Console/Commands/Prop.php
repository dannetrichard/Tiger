<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Controllers\PropController;

class Prop extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prop {opration}';

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
    public function __construct(PropController $prop)
    {
        parent::__construct();
        
        $this->prop = $prop;
        
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if($this->argument('opration')=='import'){
            $this->prop->import();
        }
    }
}
