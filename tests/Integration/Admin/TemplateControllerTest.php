<?php

namespace REBELinBLUE\Deployer\Tests\Integration\Admin;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\Repositories\Contracts\CommandRepositoryInterface;
use REBELinBLUE\Deployer\Template;
use REBELinBLUE\Deployer\Tests\AuthenticatedTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Http\Controllers\Admin\TemplateController
 */
class TemplateControllerTest extends AuthenticatedTestCase
{
    use DatabaseMigrations;

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

        /** @var \Robbo\Presenter\View\View $view */
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
        $this->markTestSkipped('not yet working');

        $name  = 'Foo';

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
