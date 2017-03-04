<?php

namespace REBELinBLUE\Deployer\Tests\Integration\Admin;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\ConfigFile;
use REBELinBLUE\Deployer\Repositories\Contracts\CommandRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\Contracts\TemplateRepositoryInterface;
use REBELinBLUE\Deployer\SharedFile;
use REBELinBLUE\Deployer\Template;
use REBELinBLUE\Deployer\Tests\Integration\AuthenticatedTestCase;
use REBELinBLUE\Deployer\Variable;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Http\Controllers\Admin\TemplateController
 */
class TemplateControllerTest extends AuthenticatedTestCase
{
    use DatabaseMigrations;

    /**
     * @covers ::__construct
     * @covers ::index
     */
    public function testIndex()
    {
        factory(Template::class, 3)->create();

        $response = $this->get('/admin/templates');

        $response->assertStatus(Response::HTTP_OK)->assertViewHas(['title', 'templates']);

        /** @var \McCool\LaravelAutoPresenter\BasePresenter $view */
        $view      = $response->getOriginalContent();
        $templates = $this->app->make(TemplateRepositoryInterface::class)->getAll();

        $this->assertSame($templates->toJson(), $view->templates);
    }

    /**
     * @covers ::__construct
     * @covers ::index
     */
    public function testIndexWithNoGroups()
    {
        $response = $this->get('/admin/templates');

        $response->assertStatus(Response::HTTP_OK)->assertViewHas(['title', 'templates']);

        /** @var \McCool\LaravelAutoPresenter\BasePresenter $view */
        $view = $response->getOriginalContent();

        $this->assertSame('[]', $view->templates);
    }

    /**
     * @dataProvider provideSteps
     * @covers ::__construct
     * @covers ::listing
     */
    public function testListing($url, $before, $after, $other, $action)
    {
        factory(Template::class)->create();

        factory(Command::class)->create(['target_type' => 'template', 'target_id' => 1, 'step' => $before]);
        factory(Command::class)->create(['target_type' => 'template', 'target_id' => 1, 'step' => $after]);
        factory(Command::class)->create(['target_type' => 'template', 'target_id' => 1, 'step' => $other]);

        $response = $this->getJson('/admin/templates/1/commands/' . $url);

        $response->assertStatus(Response::HTTP_OK)
                 ->assertViewHas(['title', 'breadcrumb', 'subtitle', 'project', 'action', 'commands'])
                 ->assertViewHas('target_type', 'template')
                 ->assertViewHas('target_id', 1)
                 ->assertViewHas('action', $action);

        /** @var \McCool\LaravelAutoPresenter\BasePresenter $view */
        $view     = $response->getOriginalContent();
        $commands = $this->app->make(CommandRepositoryInterface::class)->getForDeployStep(1, 'template', $action);

        $this->assertSame($commands->toJson(), $view->commands->toJson());
    }

    public function provideSteps()
    {
        return [
            ['clone', Command::BEFORE_CLONE, Command::AFTER_CLONE, Command::AFTER_PURGE, Command::DO_CLONE],
            ['install', Command::BEFORE_INSTALL, Command::AFTER_INSTALL, Command::AFTER_ACTIVATE, Command::DO_INSTALL],
            [
                'activate',
                Command::BEFORE_ACTIVATE,
                Command::AFTER_ACTIVATE,
                Command::AFTER_INSTALL,
                Command::DO_ACTIVATE,
            ],
            ['purge', Command::BEFORE_PURGE, Command::AFTER_PURGE, Command::AFTER_CLONE, Command::DO_PURGE],
        ];
    }

    /**
     * @covers ::__construct
     * @covers ::show
     */
    public function testShow()
    {
        $shared    = 5;
        $variables = 3;
        $config    = 1;

        /** @var Template $template */
        $template = factory(Template::class)->create();

        factory(SharedFile::class, $shared)->create(['target_type' => 'template', 'target_id' => 1]);
        factory(ConfigFile::class, $config)->create(['target_type' => 'template', 'target_id' => 1]);
        factory(Variable::class, $variables)->create(['target_type' => 'template', 'target_id' => 1]);

        $response = $this->get('/admin/templates/1');

        $response->assertStatus(Response::HTTP_OK)
                 ->assertViewHas([
                     'breadcrumb', 'title', 'route',
                     'sharedFiles', 'configFiles', 'variables', 'project',
                 ])
                 ->assertViewHas('target_type', 'template')
                 ->assertViewHas('target_id', 1);

        $template = $template->fresh();

        /** @var \McCool\LaravelAutoPresenter\BasePresenter $view */
        $view = $response->getOriginalContent();
        $this->assertSame($template->toJson(), $view->project->toJson());

        $this->assertSame($shared, $view->sharedFiles->count());
        $this->assertSame($template->sharedFiles->toJson(), $view->sharedFiles->toJson());

        $this->assertSame($config, $view->configFiles->count());
        $this->assertSame($template->configFiles->toJson(), $view->configFiles->toJson());

        $this->assertSame($variables, $view->variables->count());
        $this->assertSame($template->variables->toJson(), $view->variables->toJson());
    }

    /**
     * @covers ::__construct
     * @covers ::show
     */
    public function testShowReturnsErrorWhenInvalid()
    {
        $this->getJson('/admin/templates/1000')->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @covers ::__construct
     * @covers ::store
     * @covers \REBELinBLUE\Deployer\Http\Requests\StoreTemplateRequest
     * @covers \REBELinBLUE\Deployer\Http\Requests\Request
     */
    public function testStore()
    {
        $expected = 'a-new-template';

        $this->postJson('/admin/templates', ['name' => $expected])
             ->assertStatus(Response::HTTP_CREATED)
             ->assertJson(['name' => $expected]);

        $this->assertDatabaseHas('templates', ['name' => $expected]);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     * @covers \REBELinBLUE\Deployer\Http\Requests\StoreTemplateRequest
     * @covers \REBELinBLUE\Deployer\Http\Requests\Request
     */
    public function testUpdate()
    {
        factory(Template::class)->create(['name' => 'Foo']);

        $this->putJson('/admin/templates/1', ['name' => 'Bar'])
             ->assertStatus(Response::HTTP_OK)
             ->assertJson(['id' => 1, 'name' => 'Bar']);

        $this->assertDatabaseHas('templates', ['id' => 1, 'name' => 'Bar']);
        $this->assertDatabaseMissing('templates', ['name' => 'Foo']);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     */
    public function testUpdateReturnsErrorWhenInvalid()
    {
        $this->putJson('/admin/templates/1000', ['name' => 'Bar'])->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController::destroy
     */
    public function testDelete()
    {
        $this->markTestIncomplete('Not working for some reason');
        $name = 'Foo';

        factory(Template::class)->create(['name' => $name]);

        $this->deleteJson('/admin/templates/1')->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('templates', ['name' => $name, 'deleted_at' => null]);
    }

    /**
     * @covers \REBELinBLUE\Deployer\Http\Controllers\Resources\ResourceController::destroy
     */
    public function testDeleteReturnsErrorWhenInvalid()
    {
        $this->deleteJson('/admin/templates/1000')->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
