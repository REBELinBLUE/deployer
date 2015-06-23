<?php

namespace App\Http\Controllers;

use App\Deployment;
use App\Http\Controllers\Controller;
use App\Jobs\QueueDeployment;
use App\Project;
use Input;

/**
 * The deployment webhook controller.
 */
class WebhookController extends Controller
{
    /**
     * Handles incoming requests from Gitlab or PHPCI to trigger deploy.
     *
     * @param  string   $hash The webhook hash
     * @return Response
     */
    public function webhook($hash)
    {
        // TODO: Check for input, make sure it is a valid gitlab hook, check repo and branch are correct
        // TODO: Allow optional commands to be specified in the POST data

        // FIXME: Change to use repo
        $project = Project::where('hash', $hash)
                          ->firstOrFail();

        $success = false;
        if ($project->servers->where('deploy_code', true)->count() > 0) {
            $optional = [];

            $deployment         = new Deployment;
            $deployment->reason = Input::get('reason');
            $deployment->branch = $project->branch;

            $this->dispatch(new QueueDeployment(
                $project,
                $deployment,
                $optional
            ));

            $success = true;
        }

        return [
            'success' => $success,
        ];
    }

    /**
     * Generates a new webhook URL.
     *
     * @param  Project  $project
     * @return Response
     */
    public function refresh(Project $project)
    {
        // FIXME: Change to use repo
        $project->generateHash();
        $project->save();

        return [
            'url' => route('webhook', $project->hash),
        ];
    }
}
