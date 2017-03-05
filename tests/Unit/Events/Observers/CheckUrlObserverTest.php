<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Events\Observers;

use REBELinBLUE\Deployer\CheckUrl;
use REBELinBLUE\Deployer\Events\Observers\CheckUrlObserver;
use REBELinBLUE\Deployer\Events\UrlDown;
use REBELinBLUE\Deployer\Events\UrlUp;
use REBELinBLUE\Deployer\Jobs\RequestProjectCheckUrl;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Events\Observers\CheckUrlObserver
 */
class CheckUrlObserverTest extends TestCase
{
    private $dispatcher;

    public function setUp()
    {
        parent::setUp();

        $this->withoutEvents();

        $this->dispatcher = $this->app->make('events');
    }

    /**
     * @covers ::__construct
     * @covers ::saved
     */
    public function testSaved()
    {
        $this->expectsJobs(RequestProjectCheckUrl::class);

        /** @var CheckUrl $url */
        $url = factory(CheckUrl::class)->make([
            'status'     => CheckUrl::UNTESTED,
            'project_id' => 1,
        ]);

        $observer = new CheckUrlObserver($this->dispatcher);
        $observer->saved($url);
    }

    /**
     * @covers ::__construct
     * @covers ::saved
     */
    public function testSavedDoesNotDispatchJobIfTested()
    {
        $this->doesntExpectJobs(RequestProjectCheckUrl::class);

        /** @var CheckUrl $url */
        $url = factory(CheckUrl::class)->make([
            'status'     => CheckUrl::ONLINE,
            'project_id' => 1,
        ]);

        $observer = new CheckUrlObserver($this->dispatcher);
        $observer->saved($url);
    }

    /**
     * @covers ::__construct
     * @covers ::updated
     */
    public function testUpdatedDispatchesEventWhenOffline()
    {
        $this->expectsEvents(UrlDown::class);
        $this->doesntExpectEvents(UrlUp::class);

        /** @var CheckUrl $url */
        $url = factory(CheckUrl::class)->make([
            'status'     => CheckUrl::OFFLINE,
            'project_id' => 1,
        ]);

        $observer = new CheckUrlObserver($this->dispatcher);
        $observer->updated($url);
    }

    /**
     * @covers ::__construct
     * @covers ::updated
     */
    public function testUpdatedDoesNotDispatchEventWhenAlreadyOnline()
    {
        $this->doesntExpectEvents([UrlUp::class, UrlDown::class]);

        /** @var CheckUrl $url */
        $url = factory(CheckUrl::class)->make([
            'status'     => CheckUrl::ONLINE,
            'project_id' => 1,
        ])->syncOriginal();

        $observer = new CheckUrlObserver($this->dispatcher);
        $observer->updated($url);
    }

    /**
     * @covers ::__construct
     * @covers ::updated
     */
    public function testUpdatedDoesNotDispatchEventWhenPreviouslyUntested()
    {
        $this->doesntExpectEvents([UrlUp::class, UrlDown::class]);

        /** @var CheckUrl $url */
        $url = factory(CheckUrl::class)->make([
            'status'     => CheckUrl::UNTESTED,
            'project_id' => 1,
        ])->syncOriginal();

        $url->status = CheckUrl::ONLINE;

        $observer = new CheckUrlObserver($this->dispatcher);
        $observer->updated($url);
    }

    /**
     * @covers ::__construct
     * @covers ::updated
     */
    public function testUpdatedDispatchesEventWhenOnline()
    {
        $this->expectsEvents(UrlUp::class);
        $this->doesntExpectEvents(UrlDown::class);

        /** @var CheckUrl $url */
        $url = factory(CheckUrl::class)->make([
            'status'     => CheckUrl::OFFLINE,
            'project_id' => 1,
        ])->syncOriginal();

        $url->status = CheckUrl::ONLINE;

        $observer = new CheckUrlObserver($this->dispatcher);
        $observer->updated($url);
    }
}
