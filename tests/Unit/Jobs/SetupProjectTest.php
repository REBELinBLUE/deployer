<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Jobs;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Mockery as m;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\ConfigFile;
use REBELinBLUE\Deployer\Jobs\SetupProject;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Repositories\Contracts\TemplateRepositoryInterface;
use REBELinBLUE\Deployer\SharedFile;
use REBELinBLUE\Deployer\Template;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Variable;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Jobs\SetupProject
 */
class SetupProjectTest extends TestCase
{
    private $template_id = 1232;

    /**
     * @covers ::__construct
     * @covers ::handle
     * @covers ::getFieldsArray
     */
    public function testHandle()
    {
        $attributes = [
            'target_type' => 'template',
            'target_id'   => $this->template_id,
        ];

        $commands  = factory(Command::class, 6)->make($attributes);
        $variables = factory(Variable::class, 3)->make($attributes);
        $files     = factory(SharedFile::class, 4)->make($attributes);
        $configs   = factory(ConfigFile::class, 2)->make($attributes);

        $project = m::mock(Project::class);

        $relation = m::mock(MorphMany::class); // FIXME: Why is this equal to the number in each array?
        $project->shouldReceive('commands')->zeroOrMoreTimes()->andReturn($relation);
        $project->shouldReceive('variables')->zeroOrMoreTimes()->andReturn($relation);
        $project->shouldReceive('sharedFiles')->zeroOrMoreTimes()->andReturn($relation);
        $project->shouldReceive('configFiles')->zeroOrMoreTimes()->andReturn($relation);

        foreach ($commands as $command) {
            $data = $this->getCleanData($command->toArray());
            $relation->shouldReceive('create')->once()->with($data);
        }

        foreach ($variables as $variable) {
            $data = $this->getCleanData($variable->toArray());
            $relation->shouldReceive('create')->once()->with($data);
        }

        foreach ($files as $file) {
            $data = $this->getCleanData($file->toArray());
            $relation->shouldReceive('create')->once()->with($data);
        }

        foreach ($configs as $config) {
            $data = $this->getCleanData($config->toArray());
            $relation->shouldReceive('create')->once()->with($data);
        }

        $template = m::mock(Template::class);
        $template->shouldReceive('getAttribute')->once()->with('commands')->andReturn(collect($commands));
        $template->shouldReceive('getAttribute')->once()->with('variables')->andReturn(collect($variables));
        $template->shouldReceive('getAttribute')->once()->with('sharedFiles')->andReturn(collect($files));
        $template->shouldReceive('getAttribute')->once()->with('configFiles')->andReturn(collect($configs));

        $repository = m::mock(TemplateRepositoryInterface::class);
        $repository->shouldReceive('getById')->once()->with($this->template_id)->andReturn($template);

        $job = new SetupProject($project, $this->template_id);
        $job->handle($repository);
    }

    private function getCleanData(array $data)
    {
        return array_except($data, ['target_type', 'target_id']);
    }
}
