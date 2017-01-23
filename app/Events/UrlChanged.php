<?php

namespace REBELinBLUE\Deployer\Events;

use Illuminate\Queue\SerializesModels;
use REBELinBLUE\Deployer\CheckUrl;

/**
 * Event class which is thrown when the URL status changes.
 **/
abstract class UrlChanged
{
    use SerializesModels;

    /**
     * @var CheckUrl
     */
    public $url;

    /**
     * UrlChanged constructor.
     *
     * @param CheckUrl $url
     */
    public function __construct(CheckUrl $url)
    {
        $this->url = $url;
    }
}
