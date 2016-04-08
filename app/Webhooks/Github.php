<?php

namespace REBELinBLUE\Deployer\Webhooks;

/**
 * Class to handle integration with Github webhooks.
 */
class Github extends Webhook
{
    /**
     * Determines whether the request was from Github.
     *
     * @return boolean
     */
    public function isRequestOrigin()
    {
        return ($this->request->headers->has('X-GitHub-Event'));
    }

    /**
     * Parses the request for a push webhook body.
     *
     * @return mixed Either an array of parameters for the deployment config, or false if it is invalid.
     */
    public function handlePush()
    {
        // We only care about push events
        if ($this->request->header('X-GitHub-Event') !== 'push') {
            return false;
        }

        $payload = $this->request->json();

        // Github sends a payload when you close a pull request with a non-existent commit.
        if ($payload->has('after') && $payload->get('after') === '0000000000000000000000000000000000000000') {
            return false;
        }

        $head = $payload->get('head_commit');

        if ($payload->has('commits')) {
            $branch = str_replace('refs/heads/', '', $payload->get('ref'));

            //commit_id = head['id']
        } else {
            $branch = str_replace('refs/tags/', '', $payload->get('ref'));

            //commit_id = $payload->get('after')
        }

        // todo: should we check the following match the repository
        /*
            [repository][git_url] => git://github.com/REBELinBLUE/deployer.git
            [repository][ssh_url] => git@github.com:REBELinBLUE/deployer.git
            [repository][clone_url] => https://github.com/REBELinBLUE/deployer.git
        */

        return [
            'reason'          => $head['message'],
            'project_id'      => $project->id,
            'branch'          => $branch,
            'optional'        => [],
            'source'          => 'Github',
            'build_url'       => $head['url'],
            'commit'          => $head['id'],
            'committer'       => $head['committer']['name'],
            'committer_email' => $head['committer']['email'],

        ];
    }
}
