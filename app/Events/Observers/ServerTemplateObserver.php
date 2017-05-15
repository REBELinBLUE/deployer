<?php

namespace REBELinBLUE\Deployer\Events\Observers;

use REBELinBLUE\Deployer\ServerTemplate;

/**
 * Event observer for server template model.
 */
class ServerTemplateObserver
{
    /**
     * Called when model is being created.
     * @param ServerTemplate $template
     */
    public function creating(ServerTemplate $template)
    {
        $this->setDefaultPort($template);
    }

    /**
     * @param ServerTemplate $template
     */
    public function updating(ServerTemplate $template)
    {
        $this->setDefaultPort($template);
    }

    /**
     * @param ServerTemplate $template
     */
    private function setDefaultPort(ServerTemplate $template)
    {
        if (empty($template->port)) {
            $template->port = 22;
        }
    }
}
