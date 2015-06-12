<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * Generic Controller class.
 */
abstract class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;
}
