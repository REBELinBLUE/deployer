<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * The main page of the dashboard
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        
        // \Config::set('remote.connections.runtime.host', 'localhost');
        // \Config::set('remote.connections.runtime.username', 'vagrant');
        // \Config::set('remote.connections.runtime.password', 'vagrant');
        // // \Config::set('remote.connections.runtime.key', 'vagrant');
        // // \Config::set('remote.connections.runtime.keyphrase', 'vagrant');
        // // \Config::set('remote.connections.runtime.root', '/tmp');
         
        // \SSH::into('runtime')->run([
        //     'ls -alstr'
        // ], function($line) {
        //     echo $line . '<br />';
        // });
        


        return view('dashboard.index', [
            'title'        => 'Dashboard',
            'is_dashboard' => true
        ]);
    }
}
