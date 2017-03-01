<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Console\Commands\Installer;

use Mockery as m;
use REBELinBLUE\Deployer\Console\Commands\Installer\EnvFile;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Console\Commands\Installer\EnvFile
 */
class EnvFileTest extends TestCase
{
    private $filesystem;

    public function setUp()
    {
        parent::setUp();

        $this->filesystem = m::mock(Filesystem::class);
        $this->prev       = base_path('.env.prev');
        $this->env        = base_path('.env');
        $this->dist       = base_path('.env.dist');
    }

    /**
     * @covers ::__construct
     * @covers ::save
     */
    public function testSave()
    {
        $original_config = <<< EOF
APP_ENV=local
APP_DEBUG=true

APP_URL=https://deployer.app
SOCKET_URL=https://deployer.app
SOCKET_SSL_KEY_FILE=/var/ssl/key
SOCKET_SSL_CERT_FILE=/var/ssl/cert
SOCKET_SSL_CA_FILE=/var/ssl/ca
SOCKET_SSL_KEY_PASSPHRASE=password

DB_CONNECTION=mysql
DB_PORT=3306
DB_HOST=localhost
DB_DATABASE=deployer
DB_USERNAME=deployer
DB_PASSWORD=secret

REDIS_PASSWORD=null
GITHUB_OAUTH_TOKEN=token
TRUSTED_PROXIES=

# Comment should be removed



# As should multiple blank lines

MAIL_DRIVER=smtp
MAIL_HOST=mail.deployer.app
MAIL_PORT=2525
MAIL_USERNAME=mailuser
MAIL_PASSWORD=mailpass

EOF;

        $input = [
            'app' => [
                'env'   => 'local',
                'debug' => 'true',
                'url'   => 'http://deployer.app',
            ],
            'socket' => [
                'url' => 'http://deployer.app',
            ],
            'db' => [
                'connection' => 'sqlite',
            ],
            'mail' => [
                'driver' => 'mail',
            ],
        ];

        $updated_config = <<< EOF
APP_ENV=local
APP_DEBUG=true

APP_URL=http://deployer.app
SOCKET_URL=http://deployer.app

DB_CONNECTION=sqlite

MAIL_DRIVER=mail

EOF;

        $this->filesystem->shouldReceive('get')->once()->with($this->env)->andReturn($original_config);
        $this->filesystem->shouldReceive('put')->once()->with($this->env, $updated_config)->andReturn(true);

        $writer = new EnvFile($this->filesystem);
        $actual = $writer->save($input);

        $this->assertTrue($actual);
    }

    /**
     * @covers ::__construct
     * @covers ::update
     */
    public function testUpdate()
    {
        $original_config = <<< EOF
APP_ENV=local
APP_DEBUG=true

APP_URL=http://deployer.app
SOCKET_URL=http://deployer.app
DB_CONNECTION=sqlite

# Comment
MAIL_DRIVER=sendmail

EOF;

        $dist_config = <<< EOF
APP_ENV=production
APP_DEBUG=false

# Some comment

APP_URL=http://localhost
SOCKET_URL=http://localhost
DB_CONNECTION=mysql
DB_HOST=localhost

MAIL_DRIVER=sendmail


# More blank lines
SOMETHING_NEW=foo

EOF;

        $updated_config = <<< EOF
APP_ENV=local
APP_DEBUG=true

APP_URL=http://deployer.app
SOCKET_URL=http://deployer.app
DB_CONNECTION=sqlite

MAIL_DRIVER=sendmail

SOMETHING_NEW=foo

EOF;

        $this->filesystem->shouldReceive('get')->once()->with($this->env)->andReturn($original_config);
        $this->filesystem->shouldReceive('copy')->with($this->env, $this->prev);
        $this->filesystem->shouldReceive('copy')->with($this->dist, $this->env);
        $this->filesystem->shouldReceive('get')->once()->with($this->env)->andReturn($dist_config);
        $this->filesystem->shouldReceive('put')->with($this->env, $updated_config)->andReturn(true);
        $this->filesystem->shouldReceive('md5')->with($this->env)->andReturn('hash');
        $this->filesystem->shouldReceive('md5')->with($this->prev)->andReturn('hash');
        $this->filesystem->shouldReceive('delete')->with($this->prev);

        $writer = new EnvFile($this->filesystem);
        $actual = $writer->update();

        $this->assertTrue($actual);
    }

    /**
     * @covers ::__construct
     * @covers ::save
     */
    public function testUpdateReturnsFalseOnError()
    {
        $config = <<< EOF
APP_ENV=local
APP_DEBUG=true

APP_URL=http://deployer.app
SOCKET_URL=http://deployer.app
DB_CONNECTION=sqlite

MAIL_DRIVER=sendmail

EOF;

        $this->filesystem->shouldReceive('get')->once()->with($this->env)->andReturn($config);
        $this->filesystem->shouldReceive('copy')->with($this->env, $this->prev);
        $this->filesystem->shouldReceive('copy')->with($this->dist, $this->env);
        $this->filesystem->shouldReceive('get')->once()->with($this->env)->andReturn($config);
        $this->filesystem->shouldReceive('put')->with($this->env, $config)->andReturn(false);
        $this->filesystem->shouldReceive('md5')->with($this->env)->andReturn('hash');
        $this->filesystem->shouldReceive('md5')->with($this->prev)->andReturn('hash');
        $this->filesystem->shouldReceive('delete')->with($this->prev);

        $writer = new EnvFile($this->filesystem);
        $actual = $writer->update();

        $this->assertFalse($actual);
    }

    /**
     * @covers ::__construct
     * @covers ::save
     */
    public function testUpdateDoesNotDeleteBackupIfConfigChanged()
    {
        $config = <<< EOF
APP_ENV=local
APP_DEBUG=true

APP_URL=http://deployer.app
SOCKET_URL=http://deployer.app
DB_CONNECTION=sqlite

MAIL_DRIVER=sendmail

EOF;

        $this->filesystem->shouldReceive('get')->once()->with($this->env)->andReturn($config);
        $this->filesystem->shouldReceive('copy')->with($this->env, $this->prev);
        $this->filesystem->shouldReceive('copy')->with($this->dist, $this->env);
        $this->filesystem->shouldReceive('get')->once()->with($this->env)->andReturn($config);
        $this->filesystem->shouldReceive('put')->with($this->env, $config)->andReturn(true);
        $this->filesystem->shouldReceive('md5')->with($this->env)->andReturn('original-hash');
        $this->filesystem->shouldReceive('md5')->with($this->prev)->andReturn('backup-hash');
        $this->filesystem->shouldNotReceive('delete');

        $writer = new EnvFile($this->filesystem);
        $actual = $writer->update();

        $this->assertTrue($actual);
    }
}
