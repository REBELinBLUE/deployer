<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Project;
use App\Deployment;

use App\Commands\QueueDeployment;

use Illuminate\Http\Request;

class WebhookController extends Controller
{
    /**
     * TODO Check for input, make sure it is a valid gitlab hook, check repo and branch are correct
     * http://doc.gitlab.com/ee/web_hooks/web_hooks.html
     */
    public function webhook($hash)
    {
        $project = Project::where('hash', '=', $hash)->first();

        $success = false;
        if (!is_null($project)) {
            $this->dispatch(new QueueDeployment($project, new Deployment));

            $success = true;
        }

        return [
            'success' => $success
        ];
    }

    public function refresh($id)
    {
        $project = Project::findOrFail($id);
        
        $project->generateHash();
        $project->save();

        return [
            'success' => true,
            'url'     => route('webhook', $project->hash)
        ];
    }

}
