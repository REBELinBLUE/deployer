<?php

namespace REBELinBLUE\Deployer\Events\Observers;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Collection;
use REBELinBLUE\Deployer\CheckUrl;
use REBELinBLUE\Deployer\Events\UrlDown as UrlDownEvent;
use REBELinBLUE\Deployer\Events\UrlUp as UrlUpEvent;
use REBELinBLUE\Deployer\Jobs\RequestProjectCheckUrl;

/**
 * Event observer for CheckUrl model.
 */
class CheckUrlObserver
{
    use DispatchesJobs;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * @param Dispatcher $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Called when the model is saved.
     *
     * @param CheckUrl $url
     */
    public function saved(CheckUrl $url)
    {
        if ($url->status === CheckUrl::UNTESTED) {
            $collection = new Collection([$url]);

            $this->dispatch(new RequestProjectCheckUrl($collection));
        }
    }

    /**
     * Called when the model is updated.
     *
     * @param CheckUrl $url
     */
    public function updated(CheckUrl $url)
    {
        if ($url->status === CheckUrl::OFFLINE) {
            $this->dispatcher->dispatch(new UrlDownEvent($url));
        } elseif ($url->status === CheckUrl::ONLINE && $url->getOriginal('status') === CheckUrl::OFFLINE) {
            $this->dispatcher->dispatch(new UrlUpEvent($url));
        }
    }
}
