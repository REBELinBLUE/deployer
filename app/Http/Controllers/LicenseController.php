<?php

namespace REBELinBLUE\Deployer\Http\Controllers;

use REBELinBLUE\Deployer\Http\Controllers\Controller;

/**
 * License controller.
 */
class LicenseController extends Controller
{
    /**
     * Show the license page.
     *
     * @return Response
     */
    public function expired()
    {
        return view('license');
    }
}
