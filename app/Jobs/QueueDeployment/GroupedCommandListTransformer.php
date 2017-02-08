<?php

namespace REBELinBLUE\Deployer\Jobs\QueueDeployment;

use Illuminate\Support\Collection;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Project;

/**
 * Groups commands by their stages.
 */
class GroupedCommandListTransformer
{
    /**
     * Takes a project and groups the commands by the deployment step they belong to.
     *
     * @param Project $project
     *
     * @return Collection
     */
    public function groupCommandsByDeployStep(Project $project)
    {
        $grouped = new Collection([
            Command::DO_CLONE    => $this->emptyStep(),
            Command::DO_INSTALL  => $this->emptyStep(),
            Command::DO_ACTIVATE => $this->emptyStep(),
            Command::DO_PURGE    => $this->emptyStep(),
        ]);

        $project->commands->each(function ($command) use ($grouped) {
            $step = $this->step($command);
            $when = $this->when($command);

            $grouped->get($step)->get($when)->push($command);
        });

        return $grouped;
    }

    /**
     * @return Collection
     */
    private function emptyStep()
    {
        return new Collection([
            'before' => new Collection(),
            'after'  => new Collection(),
        ]);
    }

    /**
     * Determines which of the 4 deployment steps the command belongs to.
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
