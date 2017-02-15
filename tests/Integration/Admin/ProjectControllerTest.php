<?php

namespace REBELinBLUE\Deployer\Tests\Integration\Admin;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Repositories\Contracts\GroupRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\ProjectRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\TemplateRepositoryInterface;
use REBELinBLUE\Deployer\Tests\AuthenticatedTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Http\Controllers\Admin\ProjectController
 * @fixme test validations, test index with data, test create with template and private key
 */
class ProjectControllerTest extends AuthenticatedTestCase
{
    use DatabaseMigrations;

    /**
     * @covers ::__construct
     * @covers ::index
     */
    public function testIndex()
    {
        $response = $this->get('/admin/projects');

        $response->assertStatus(Response::HTTP_OK)
                 ->assertViewHas(['is_secure', 'title', 'templates', 'groups', 'projects']);

        /** @var \Robbo\Presenter\View\View $view */
        $view      = $response->getOriginalContent();
        $projects  = app(ProjectRepositoryInterface::class)->getAll();
        $templates = app(TemplateRepositoryInterface::class)->getAll();
        $groups    = app(GroupRepositoryInterface::class)->getAll();

        $this->assertSame($projects->toJson(), $view->projects);
        $this->assertSame($templates->toJson(), $view->templates->toJson());
        $this->assertSame($groups->toJson(), $view->groups->toJson());
    }

    /**
     * @covers ::__construct
     * @covers ::store
     * @covers \REBELinBLUE\Deployer\Http\Requests\StoreProjectRequest
     * @covers \REBELinBLUE\Deployer\Http\Requests\Request
     */
    public function testStore()
    {
        $input = [
            'name'               => 'My Site',
            'repository'         => 'git@git.example.com:namespace/repository.git',
            'branch'             => 'master',
            'group_id'           => 1,
            'builds_to_keep'     => 5,
            'url'                => 'http://www.example.com',
            'build_url'          => 'http://ci.example.com/build.png',
            'allow_other_branch' => true,
            'include_dev'        => false,
            'template_id'        => '',
        ];

        $output = array_merge([
            'id' => 1,
        ], array_except($input, ['template_id']));

        $this->postJson('/admin/projects', $input)->assertStatus(Response::HTTP_CREATED)->assertJson($output);

        $this->assertDatabaseHas('projects', $output);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     * @covers \REBELinBLUE\Deployer\Http\Requests\StoreProjectRequest
     * @covers \REBELinBLUE\Deployer\Http\Requests\Request
     */
    public function testUpdate()
    {
        $original = 'My Site';
        $updated  = 'Renamed project';

        /** @var Project $project */
        $project = factory(Project::class)->create([
            'name'      => $original,
            'url'       => 'http://www.example.com',
            'build_url' => 'http://ci.example.com/build.png',
        ]);

        $data = array_only($project->fresh()->toArray(), [
            'name',
            'repository',
            'branch',
            'group_id',
            'builds_to_keep',
            'url',
            'build_url',
            'allow_other_branch',
            'include_dev',
        ]);

        $input = array_merge($data, [
            'name' => $updated,
        ]);

        $this->putJson('/admin/projects/1', $input)->assertStatus(Response::HTTP_OK)->assertJson($input);

        $this->assertDatabaseHas('projects', ['name' => $updated]);
        $this->assertDatabaseMissing('projects', ['name' => $original]);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     */
    public function testUpdateReturnsErrorWhenInvalid()
    {
        $input = [
            'name'               => 'My Site',
            'repository'         => 'git@git.example.com:namespace/repository.git',
            'branch'             => 'master',
            'group_id'           => 1,
            'builds_to_keep'     => 5,
            'url'                => 'http://www.example.com',
            'build_url'          => 'http://ci.example.com/build.png',
            'allow_other_branch' => true,
            'include_dev'        => false,
        ];

        $this->putJson('/admin/projects/1000', $input)->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController::destroy
     */
    public function testDelete()
    {
        $name = 'A test project';

        factory(Project::class)->create(['name' => $name]);

        $this->deleteJson('/admin/projects/1')->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('projects', ['name' => $name, 'deleted_at' => null]);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController::destroy
     */
    public function testDeleteReturnsErrorWhenInvalid()
    {
        $this->deleteJson('/admin/projects/1000')->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
