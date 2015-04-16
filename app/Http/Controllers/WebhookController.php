<?php namespace App\Http\Controllers;

use App\Project;
use App\Deployment;
use App\Commands\QueueDeployment;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * The deployment webhook controller
 */
class WebhookController extends Controller
{
    /**
     * Handles incoming requests from Gitlab or PHPCI to trigger deploy
     *
     * @param string $hash The webhook hash
     * @return Response
     * @todo Check for input, make sure it is a valid gitlab hook, check repo and branch are correct
     *       http://doc.gitlab.com/ee/web_hooks/web_hooks.html
     */
    public function webhook($hash)
    {
        $project = Project::where('hash', $hash)
                          ->firstOrFail();

        if ($project->servers->count() > 0) { // FIXME: This should filter to deployable servers
            $optional = []; // FIXME: Come up with some way to specify which commands should run or not

            $this->dispatch(new QueueDeployment(
                $project,
                new Deployment,
                $optional
            ));
        }

        return [
            'success' => true
        ];
    }

    /**
     * Generates a new webhook URL
     *
     * @param Project $project
     * @return Response
     */
    public function refresh(Project $project)
    {
        $project->generateHash();
        $project->save();

        return [
            'url' => route('webhook', $project->hash)
        ];
    }
}
