<?php

namespace REBELinBLUE\Deployer\Jobs\QueueDeployment;

use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Project;

/**
 * Groups commands by their stages.
 */
class GroupedCommandListBuilder
{
    /**
     * Takes a project and groups the commands by the stages they belong to.
     *
     * @param Project $project
     *
     * @return array
     */
    public function groupCommandsByStep(Project $project)
    {
        $grouped = [
            Command::DO_CLONE    => null,
            Command::DO_INSTALL  => null,
            Command::DO_ACTIVATE => null,
            Command::DO_PURGE    => null,
        ];

        foreach ($project->commands as $command) {
            $step   = $this->step($command);
            $when   = $this->when($command);

            if (!is_array($grouped[$step])) {
                $grouped[$step] = [];
            }

            if (!isset($hooks[$step][$when])) {
                $grouped[$step][$when] = [];
            }

            $grouped[$step][$when][] = $command;
        }

        return $grouped;
    }

    /**
     * Determines which of the 4 steps the command belongs to.
     *
     * @param Command $command
     *
     * @return int
     */
    private function step(Command $command)
    {
        if ($command->step % 3 === 0) {
            return $command->step - 1;
        }

        return $command->step + 1;
    }

    /**
     * Determines if the command is for the before or after stage.
     *
     * @param Command $command
     *
     * @return string
     */
    private function when(Command $command)
    {
        return ($command->step % 3 === 0 ? 'after' : 'before');
    }
}
