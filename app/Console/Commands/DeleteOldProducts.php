<?php

namespace App\Console\Commands;

use App\Services\DeleteOldProductServiceTrait;
use Illuminate\Console\Command;
use App\Services\UpdatePriceService;

class DeleteOldProducts extends Command
{
    use DeleteOldProductServiceTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:old_products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function __invoke(UpdatePriceService $service)
    {
        $this->service = $service;
        $this->delete();
        echo "Setting mix and max prices for products\n";
        $this->service->setMinAndMaxPriceForAllProducts();
        echo "Setting mix and max prices for products completed\n";
    }
}
