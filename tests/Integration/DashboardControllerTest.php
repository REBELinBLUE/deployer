<?php

namespace REBELinBLUE\Deployer\tests\Integration;

use Carbon\Carbon;
use DOMDocument;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Project;
use REBELinBLUE\Deployer\Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Http\Controllers\DashboardController
 */
class DashboardControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @covers ::__construct
     * @covers ::cctray
     */
    public function testCctray()
    {
        factory(Project::class)->create(['status' => Project::NOT_DEPLOYED]);

        $date    = Carbon::create(2016, 10, 12, 19, 56, 00, 'UTC');
        $project = factory(Project::class)->create([
            'status'   => Project::FINISHED,
            'last_run' => $date,
        ]);

        factory(Deployment::class)->create([
            'project_id' => $project->id,
            'status'     => Deployment::COMPLETED,
        ]);

        $response = $this->dontSeeIsAuthenticated()->get('/cctray.xml', ['Accept' => 'application/xml']);

        $response->assertStatus(Response::HTTP_OK)
                 ->assertHeader('Content-Type', 'application/xml');

        $expected = new DOMDocument();
        $expected->loadXML('<Projects><Project/><Project/></Projects>');

        $actual = new DOMDocument();
        $actual->loadXML($response->getContent());

        // FIXME: Change this to also care about attributes!
        $this->assertEqualXMLStructure($expected->firstChild, $actual->firstChild);
    }
}
