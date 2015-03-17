<?php namespace App\Commands;

use App\Commands\Command;

use App\Server;

use Config;
use SSH;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;

class TestServerConnection extends Command implements SelfHandling, ShouldBeQueued {

    use InteractsWithQueue, SerializesModels;

    public $server;

    /**
     * Create a new command instance.
     *
     * @return void
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
        $this->server->status = 'Testing';
        $this->server->save();

        $key = tempnam(storage_path() . '/app/', 'sshkey');
        file_put_contents($key, $this->server->project->private_key);

        Config::set('remote.connections.runtime.host', $this->server->ip_address);
        Config::set('remote.connections.runtime.username', $this->server->user);
        Config::set('remote.connections.runtime.password', '');
        Config::set('remote.connections.runtime.key', $key);
        Config::set('remote.connections.runtime.keyphrase', '');
        Config::set('remote.connections.runtime.root', $this->server->path);

        try
        {
            SSH::into('runtime')->run([
                'ls'
            ]);

            $this->server->status = 'Successful';
        }
        catch (\Exception $error)
        {
            echo $error;
            $this->server->status = 'Failed';
        }

        $this->server->save();

        unlink($key);
    }

}
