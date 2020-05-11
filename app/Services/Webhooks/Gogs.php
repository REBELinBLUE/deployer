<?php

namespace REBELinBLUE\Deployer\Services\Webhooks;

/**
 * Class to handle integration with Gogs/Gitea webhooks.
 */
class Gogs extends Webhook
{
    /**
     * Determines whether the request was from Gogs/Gitea.
     *
     * @return bool
     */
    public function isRequestOrigin(): bool
    {
        return ($this->request->headers->has('X-Gogs-Event'));
    }

    /**
     * Parses the request for a push webhook body.
     *
     * @return mixed Either an array of parameters for the deployment config, or false if it is invalid.
     */
    public function handlePush()
    {
        // We only care about push events
        if ($this->request->header('X-Gogs-Event') !== 'push') {
            return false;
        }

        $payload = $this->request->json();
        $commits = $payload->get('commits');

        if (!is_array($commits) || count($commits) === 0) {
            return false;
        }

        $head   = array_shift($commits);
        $branch = preg_replace('#refs/(tags|heads)/#', '', $payload->get('ref'));

        return [
            'reason'          => trim($head['message']),
            'branch'          => $branch,
            'source'          => 'Gogs',
            'build_url'       => $head['url'],
            'commit'          => $head['id'],
            'committer'       => $head['committer']['name'],
            'committer_email' => $head['committer']['email'],
        ];
    }
}
