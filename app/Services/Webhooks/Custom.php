<?php

namespace REBELinBLUE\Deployer\Services\Webhooks;

/**
 * Class to handle integration with Custom webhooks.
 */
class Custom extends Webhook
{
    /**
     * Determines whether the request was for the Custom webhook.
     *
     * @return bool
     */
    public function isRequestOrigin(): bool
    {
        return true;
    }

    /**
     * Parses the request for a webhook body.
     *
     * @return mixed Either an array of parameters for the deployment config, or false if it is invalid.
     */
    public function handlePush()
    {
        // Get the branch if it is the request, otherwise deploy the default branch
        $branch = $this->request->has('branch') ? $this->request->get('branch') : null;

        // If there is a source and a URL validate that the URL is valid
        $build_url = null;
        if ($this->request->has('source') && $this->request->has('url')) {
            $build_url = $this->request->get('url');

            if (!filter_var($build_url, FILTER_VALIDATE_URL)) {
                $build_url = null;
            }
        }

        $commit = '';
        if ($this->request->has('commit')) {
            $commit = $this->request->get('commit');

            if (strlen($commit) < 7) {
                $commit = '';
            }
        }

        return [
            'reason'    => $this->request->get('reason'),
            'branch'    => $branch,
            'source'    => $this->request->get('source'),
            'build_url' => $build_url,
            'commit'    => $commit,
        ];
    }
}
