<?php

namespace REBELinBLUE\Deployer\Services\Webhooks;

/**
 * Class to handle integration with Gitlab webhooks.
 */
class Gitlab extends Webhook
{
    /**
     * Determines whether the request was from Gitlab.
     *
     * @return bool
     */
    public function isRequestOrigin(): bool
    {
        return ($this->request->headers->has('X-Gitlab-Event'));
    }

    /**
     * Parses the request for a push webhook body.
     *
     * @return mixed Either an array of parameters for the deployment config, or false if it is invalid.
     */
    public function handlePush()
    {
        // We only care about "Tag Push Hook" & "Push Hook" events
        if (strpos($this->request->header('X-Gitlab-Event'), 'Push Hook') === false) {
            return false;
        }

        $payload = $this->request->json();

        // Sort the commits by the timestamp descending order and then get the first one
        $head = collect($payload->get('commits'))->sortByDesc(function ($commit) {
            return strtotime($commit['timestamp']);
        })->first();

        $branch = preg_replace('#refs/(tags|heads)/#', '', $payload->get('ref'));

        return [
            'reason'          => trim($head['message']),
            'branch'          => $branch,
            'source'          => 'Gitlab',
            'build_url'       => $head['url'],
            'commit'          => $head['id'],
            'committer'       => $head['author']['name'],
            'committer_email' => $head['author']['email'],
        ];
    }
}
