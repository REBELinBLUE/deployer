<?php

namespace REBELinBLUE\Deployer\Tests\Integration;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\View\Presenters\DeploymentPresenter;
use REBELinBLUE\Deployer\View\Presenters\ProjectPresenter;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Http\Controllers\DashboardController
 */
class DashboardControllerTest extends AuthenticatedTestCase
{
    use DatabaseMigrations;

    /**
     * @covers ::index
     * @covers ::buildTimelineData
     */
    public function testIndex()
    {
        /** @var Project $project */
        $project = factory(Project::class)->create([
            'name'     => 'My Test Project',
            'group_id' => 1,
        ])->fresh();

        $response = $this->get('/');

        $response->assertStatus(Response::HTTP_OK)->assertViewHas(['title', 'latest', 'projects']);

        /** @var \McCool\LaravelAutoPresenter\BasePresenter $view */
        $view          = $response->getOriginalContent();
        $projects      = $view->projects;
        $expectedGroup = 'Projects';

        $this->assertInternalType('array', $projects);
        $this->assertArrayHasKey($expectedGroup, $projects);
        $this->assertInternalType('array', $projects[$expectedGroup]);
        $this->assertCount(1, $projects[$expectedGroup]);
        $this->assertContainsOnlyInstancesOf(ProjectPresenter::class, $projects[$expectedGroup]);
        $this->assertSame($project->toJson(), $projects[$expectedGroup][0]->toJson());
    }

    /**
     * @covers ::timeline
     * @covers ::buildTimelineData
     */
    public function testTimeline()
    {
        /** @var Project $project */
        $project = factory(Project::class)->create([
            'name'     => 'My Test Project',
            'group_id' => 1,
        ]);

        $expectedOnA = 2;
        $expectedOnB = 3;

        factory(Deployment::class, $expectedOnA)->create([
            'project_id' => $project->id,
            'started_at' => Carbon::create(2017, 2, 5),
        ]);

        factory(Deployment::class, $expectedOnB)->create([
            'project_id' => $project->id,
            'started_at' => Carbon::create(2017, 2, 1),
        ]);

        $response = $this->get('/timeline');
        $response->assertStatus(Response::HTTP_OK)->assertViewHasAll(['latest']);

        /** @var \McCool\LaravelAutoPresenter\BasePresenter $view */
        $view = $response->getOriginalContent();

        $this->assertInternalType('array', $view->latest);
        $this->assertArrayHasKey('2017-02-01', $view->latest);
        $this->assertArrayHasKey('2017-02-05', $view->latest);
        $this->assertCount(2, $view->latest);

        $this->assertInternalType('array', $view->latest['2017-02-05']);
        $this->assertInternalType('array', $view->latest['2017-02-01']);
        $this->assertCount($expectedOnA, $view->latest['2017-02-05']);
        $this->assertCount($expectedOnB, $view->latest['2017-02-01']);

        $this->assertContainsOnlyInstancesOf(DeploymentPresenter::class, $view->latest['2017-02-05']);
        $this->assertContainsOnlyInstancesOf(DeploymentPresenter::class, $view->latest['2017-02-01']);
    }
}
