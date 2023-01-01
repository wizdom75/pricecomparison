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
    public function __construct()
    {
        parent::__construct();
        $this->service = new UpdatePriceService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->delete();
        echo "Setting min and max prices for products\n";
        $this->service->setMinAndMaxPriceForAllProducts();
        echo "Setting min and max prices for products completed\n";
    }

    public function __invoke(UpdatePriceService $service)
    {
        $this->delete();
        echo "Setting min and max prices for products\n";
        $service->setMinAndMaxPriceForAllProducts();
        echo "Setting min and max prices for products completed\n";
    }
}
