<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DownloadProductImagesServiceTrait;

class DownloadProductImages extends Command
{
    use DownloadProductImagesServiceTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'download:images  {--product_id=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download product images';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function __invoke()
    {
        $this->downloadAll();
    }
}
