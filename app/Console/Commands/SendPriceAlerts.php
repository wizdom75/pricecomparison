<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SendPriceAlertsServiceTrait;

class SendPriceAlerts extends Command
{
    use SendPriceAlertsServiceTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:price_alerts  {--user_id=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends price alerts to customers';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->sendPriceAlerts();
    }
}
