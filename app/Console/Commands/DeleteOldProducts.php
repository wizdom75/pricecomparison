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
