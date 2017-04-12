<?php
/**
 * Created by PhpStorm.
 * User: claudiopinto
 * Date: 11/04/2017
 * Time: 22:59
 */

namespace REBELinBLUE\Deployer\Events\Observers;


use REBELinBLUE\Deployer\ServerTemplate;

class ServerTemplateObserver
{

    /**
     * Called when model is being created
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