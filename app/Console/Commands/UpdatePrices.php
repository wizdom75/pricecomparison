<?php

namespace App\Console\Commands;

use App\Services\UpdatePriceService;
use Illuminate\Console\Command;

class UpdatePrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:prices {--merchant_id=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update prices by datafeed';

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
        $ids = $this->option('merchant_id');
        echo "Updating prices by datafeed\n";

         $this->service->run($ids);

         echo "Price updating is complete\n";

         echo "Setting mix and max prices for products\n";
         $this->service->setMinAndMaxPriceForAllProducts();
         echo "Setting mix and max prices for products completed\n";
    }

    public function __invoke(UpdatePriceService $service)
    {
        $this->service = $service;
        echo "Updating prices by datafeed\n";

         $this->service->run([]);

         echo "Price updating is complete\n";

         echo "Setting mix and max prices for products\n";
         $this->service->setMinAndMaxPriceForAllProducts();
         echo "Setting mix and max prices for products completed\n";
    }
}
