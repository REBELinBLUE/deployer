<?php

namespace REBELinBLUE\Deployer\Scripts;

/**
 * Class which loads a shell script template and parses any variables.
**/
class Parser
{
    private $step;
    private $server;

    public function __construct(DeployStep $step, Server $server)
    {
        $this->step = $step;
        $this->server = $server;
    }
}
