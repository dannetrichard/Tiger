<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Controllers\TbRefundController;

class Refund extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refund {opration}';

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
    public function __construct(TbRefundController $refund)
    {
        parent::__construct();
        
        $this->refund = $refund;
        
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if($this->argument('opration')=='import'){
            $this->refund->import();
        }elseif($this->argument('opration')=='refresh'){
            $this->refund->refresh();
        }elseif($this->argument('opration')=='data'){
            $this->refund->data();
        }
    }
}
