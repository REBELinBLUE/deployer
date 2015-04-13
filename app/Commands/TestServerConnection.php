<?php namespace App\Commands;

use Config;
use SSH;
use App\Server;
use App\Commands\Command;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;

/**
 * Tests if a server can successfully be SSHed into
 */
class TestServerConnection extends Command implements SelfHandling, ShouldBeQueued
{
    use InteractsWithQueue, SerializesModels;

    public $server;

    /**
     * Create a new command instance.
     *
     * @param Server $server
     * @return TestServerConnection
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $this->server->status = Server::TESTING;
        $this->server->save();

        $key = tempnam(storage_path() . '/app/', 'sshkey');
        file_put_contents($key, $this->server->project->private_key);

        Config::set('remote.connections.runtime.host', $this->server->ip_address);
        Config::set('remote.connections.runtime.username', $this->server->user);
        Config::set('remote.connections.runtime.password', '');
        Config::set('remote.connections.runtime.key', $key);
        Config::set('remote.connections.runtime.keyphrase', '');
        Config::set('remote.connections.runtime.root', $this->server->path);

        //echo sprintf('Connecting to server #%s %s (%s)',
        //             $this->server->id,
        //             $this->server->name,
        //             $this->server->ip_address) . PHP_EOL;

        try {
            SSH::into('runtime')->run([
                'ls'
            ]);

            $this->server->status = Server::SUCCESSFUL;
        } catch (\Exception $error) {
            $this->server->status = Server::FAILED;
        }

        $this->server->save();

        unlink($key);
    }
}
