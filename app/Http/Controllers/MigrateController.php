<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class MigrateController extends Controller
{
    public function run()
    {
        Artisan::call('migrate', array('--path' => 'app/migrations', '--force' => true));
    }

}
