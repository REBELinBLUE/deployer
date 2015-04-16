<?php namespace App\Commands;

use App\Project;
use App\Template;
use App\Command as Stage;
use App\Commands\Command;

use Illuminate\Contracts\Bus\SelfHandling;

/**
 * A class to handle cloning the command templates for the project
 */
class SetupProject extends Command implements SelfHandling
{
    private $project;
    private $template;

    /**
     * Create a new command instance.
     *
     * @param Project $project
     * @param Template $template
     * @return SetupProject
     */
    public function __construct(Project $project, Template $template)
    {
        $this->project = $project;
        $this->template = $template;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->template->commands as $command)
        {
            $data = $command->toArray();

            $data['project_id'] = $this->project->id;
            unset($data['template_id']);

            Stage::create($data);
        }
    }
}
