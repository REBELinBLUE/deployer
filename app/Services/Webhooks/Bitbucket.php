<?php

namespace REBELinBLUE\Deployer\Services\Webhooks;

/**
 * Class to handle integration with Bitbucket webhooks.
 */
class Bitbucket extends Webhook
{
    /**
     * Determines whether the request was from Bitbucket.
     *
     * @return bool
     */
    public function isRequestOrigin()
    {
        return ($this->request->headers->has('X-Event-Key'));
    }

    /**
     * Parses the request for a push webhook body.
     *
     * @return mixed Either an array of parameters for the deployment config, or false if it is invalid.
     */
    public function handlePush()
    {
        // We only care about push events
        if ($this->request->header('X-Event-Key') !== 'repo:push') {
            return false;
        }

        $payload = $this->request->json();
        $push    = $payload->get('push');

        if (!array_key_exists('changes', $push)) {
            return false;
        }

        $changes = $push['changes'];
        if (!count($changes)) {
            return false;
        }

        $head = $changes[0]['new'];

        list($name, $email) = explode(' <', trim($head['target']['author']['raw'], '> '));

        return [
            'reason'          => trim($head['target']['message']),
            'branch'          => $head['name'],
            'source'          => 'Bitbucket',
            'build_url'       => $head['target']['links']['html']['href'],
            'commit'          => $head['target']['hash'],
            'committer'       => $name,
            'committer_email' => $email,
        ];
    }
}
