<?php

namespace REBELinBLUE\Deployer\Providers;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Foundation\Application;
use Illuminate\Notifications\Channels\SlackWebhookChannel;
use Illuminate\Support\ServiceProvider;
use NotificationChannels\Webhook\WebhookChannel;
use function GuzzleHttp\default_user_agent;

/**
 * Provides Guzzle client.
 */
class GuzzleServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register()
    {
        $client = $this->getClient();

        // Inject the Guzzle client for the Webhook channel so that we can set some defaults
        $this->app->when(WebhookChannel::class)
                   ->needs(HttpClient::class)
                   ->give(function (Application $app) use ($client) {
                       return $client($app, [
                           'headers' => ['Content-Type' => 'application/json'],
                       ]);
                   });

        $this->app->alias('HttpClient', HttpClient::class);
        $this->app->when(SlackWebhookChannel::class)->needs(HttpClient::class)->give('HttpClient');

        $this->app->bind('HttpClient', function (Application $app) use ($client) {
            return $client($app);
        });
    }

    /**
     * Creates a closure for the Guzzle client so that additional options can be provided.
     *
     * @return \Closure
     */
    private function getClient()
    {
        return function (Application $app, array $additional = []) {
            $config = array_merge($app->make('config')->get('deployer.guzzle') ?: [], [
                'headers' => ['User-Agent' => 'Deployer/' . APP_VERSION . ' ' . default_user_agent()],
            ]);

            return new HttpClient(array_merge_recursive($config, $additional));
        };
    }
}
