<?php namespace App\Commands;

use App\Commands\Command;

use Httpful\Request;

use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Request the urls
 */
class RequestProjectCheckUrl extends Command implements SelfHandling, ShouldBeQueued
{
    use InteractsWithQueue, SerializesModels;

    private $urls;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($urls)
    {
        $this->urls = $urls;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->urls as $link) {
            $reponse = Request::get($link->url)->send();
            $link->last_status = $reponse->hasErrors();
            $link->save();
        }
    }
}
