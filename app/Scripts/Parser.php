<?php

namespace REBELinBLUE\Deployer\Scripts;


use REBELinBLUE\Deployer\Command as Stage;

/**
 * Class which loads a shell script template and parses any variables.
**/
class Parser
{
    private $step;
    private $server;

    /**
     * Class constructor.
     *
     * @param DeployStep $step
     * @param Server $server
     */
    public function __construct(DeployStep $step, Server $server)
    {
        $this->step = $step;
        $this->server = $server;
    }

    public function getScript()
    {
        return $this->getScriptForStep($this->step);
    }

    /**
     * Gets the script which is used for the supplied step.
     *
     * @param  DeployStep $step
     * @return string
     */
    private function getScriptForStep(DeployStep $step)
    {
        switch ($step->stage) {
            case Stage::DO_CLONE:
                return $this->loadScriptFromTemplate('CreateNewRelease');
            case Stage::DO_INSTALL:
                return $this->loadScriptFromTemplate('InstallComposerDependencies');
            case Stage::DO_ACTIVATE:
                return $this->loadScriptFromTemplate('ActivateNewRelease');
            case Stage::DO_PURGE:
                return $this->loadScriptFromTemplate('PurgeOldReleases');
        }

        // Custom step
        return $step->command->script;
    }

    /**
     * Loads a script from a template file.
     *
     * @param  string $template
     * @return string
     * @throws RuntimeException
     */
    private function loadScriptFromTemplate($template)
    {
        $template = resource_path('scripts/' . $template . '.sh');

        if (file_exists($template)) {
            return file_get_contents($template);
        }

        throw new \RuntimeException('Template ' . $template . ' does not exist');
    }
}
