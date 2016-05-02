<?php

namespace REBELinBLUE\Deployer\Webhooks;

/**
 * Class to handle integration with Beanstalkapp webhooks.
 */
class Beanstalkapp extends Webhook
{
    /**
     * Determines whether the request was from Beanstalkapp.
     *
     * @return bool
     */
    public function isRequestOrigin()
    {
        return ($this->request->headers->get('User-Agent') === 'beanstalkapp.com');
    }

    /**
     * Parses the request for a push webhook body.
     *
     * @return mixed Either an array of parameters for the deployment config, or false if it is invalid.
     */
    public function handlePush()
    {
        $payload = $this->request->json();

        // Beanstalk is different to the other services, trigger is not in the headers but in the payload
        if (!$payload->has('trigger')) {
            return false;
        }

        // We only create about push and tag events
        $trigger = $payload->get('trigger');
        if (!in_array($trigger, ['push', 'create_tag'], true)) {
            return false;
        }

        return false;

        // Github sends a payload when you close a pull request with a non-existent commit.
        if ($payload->has('after') && $payload->get('after') === '0000000000000000000000000000000000000000') {
            return false;
        }

        $head   = $payload->get('head_commit');
        $branch = preg_replace('#refs/(tags|heads)/#', '', $payload->get('ref'));

        return [
            'reason'          => $head['message'],
            'branch'          => $branch,
            'source'          => 'Beanstalkapp',
            'build_url'       => $head['url'],
            'commit'          => $head['id'],
            'committer'       => $head['committer']['name'],
            'committer_email' => $head['committer']['email'],
        ];
    }
}
