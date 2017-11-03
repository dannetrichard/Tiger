<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Controllers\ProductController;

class Product extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product {opration} {--shop=*} {--from=1} {--to=0}';

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
    public function __construct(ProductController $product)
    {
        parent::__construct();
        $this->product = $product;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if($this->argument('opration')=='import'){
            $shop = $this->option('shop');
            $from = $this->option('from');
            $to = $this->option('to');
            $this->product->import($shop,$from,$to);
        }elseif($this->argument('opration')=='export'){
            $this->product->export();
        }elseif($this->argument('opration')=='clear'){
            $this->product->clear();
        }elseif($this->argument('opration')=='last'){
            $shop = $this->option('shop');
            $this->product->last($shop);
        }
    }
}
