<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\UpdatePriceService;
use App\Services\DeleteOldPriceServiceTrait;

class DeleteOldPrices extends Command
{
    use DeleteOldPriceServiceTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:old_prices   {--merchant_id=*}';

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
    public function __construct(UpdatePriceService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->delete();
        echo "Setting mix and max prices for products\n";
        $this->service->setMinAndMaxPriceForAllProducts();
        echo "Setting mix and max prices for products completed\n";
    }
}
