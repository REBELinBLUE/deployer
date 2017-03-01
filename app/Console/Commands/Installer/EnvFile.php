<?php

namespace REBELinBLUE\Deployer\Console\Commands\Installer;

use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;

/**
 * Class to update the .env file.
 */
class EnvFile
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * EnvFile constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Saves the config file.
     *
     * @param array $input
     *
     * @return int
     */
    public function save(array $input)
    {
        $path   = base_path('.env');
        $config = $this->filesystem->get($path);

        foreach ($input as $section => $data) {
            foreach ($data as $key => $value) {
                $env = strtoupper($section . '_' . $key);

                $config = preg_replace('/' . $env . '=(.*)/', $env . '=' . $value, $config);
            }
        }

        // Remove SSL certificate keys if not using HTTPS
        if (substr($input['socket']['url'], 0, 5) !== 'https') {
            foreach (['key', 'cert', 'ca'] as $key) {
                $key = strtoupper($key);

                $config = preg_replace('/SOCKET_SSL_' . $key . '_FILE=(.*)[\n]/', '', $config);
            }

            $config = preg_replace('/SOCKET_SSL_KEY_PASSPHRASE=(.*)[\n]/', '', $config);
        }

        // Remove keys not needed for sqlite
        if ($input['db']['connection'] === 'sqlite') {
            foreach (['host', 'database', 'username', 'password', 'port'] as $key) {
                $key = strtoupper($key);

                $config = preg_replace('/DB_' . $key . '=(.*)[\n]/', '', $config);
            }
        }

        // Remove keys not needed by SMTP
        if ($input['mail']['driver'] !== 'smtp') {
            foreach (['host', 'port', 'username', 'password'] as $key) {
                $key = strtoupper($key);

                $config = preg_replace('/MAIL_' . $key . '=(.*)[\n]/', '', $config);
            }
        }

        // Remove redis password if null
        $config = preg_replace('/REDIS_PASSWORD=null[\n]/', '', $config);

        // Remove github keys if not needed, only really exists on my dev copy
        if (!isset($input['github']) || empty($input['github']['oauth_token'])) {
            $config = preg_replace('/GITHUB_OAUTH_TOKEN=(.*)[\n]/', '', $config);
        }

        // Remove trusted_proxies if not set
        if (!isset($input['trusted']) || !isset($input['trusted']['proxied'])) {
            $config = preg_replace('/TRUSTED_PROXIES=(.*)[\n]/', '', $config);
        }

        // Remove comments
        $config = preg_replace('/#(.*)[\n]/', '', $config);
        $config = preg_replace('/[\n]{3,}/m', PHP_EOL . PHP_EOL, $config);

        return $this->filesystem->put($path, trim($config) . PHP_EOL);
    }

    /**
     * Checks for new configuration values in .env.dist and copy them to .env.
     *
     * @return bool
     */
    public function update()
    {
        $prev     = base_path('.env.prev');
        $current  = base_path('.env');
        $dist     = base_path('.env.dist');

        $config = [];

        // Read the current config values into an array for the writeEnvFile method
        $content = $this->filesystem->get($current);
        foreach (explode(PHP_EOL, $content) as $line) {
            $line = trim($line);

            if (empty($line)) {
                continue;
            }

            $parts = explode('=', $line);

            if (count($parts) < 2) {
                continue;
            }

            $env   = strtolower($parts[0]);
            $value = trim($parts[1]);

            $section = substr($env, 0, strpos($env, '_'));
            $key     = substr($env, strpos($env, '_') + 1);

            $config[$section][$key] = $value;
        }

        // Backup the .env file, just in case it failed because we don't want to lose APP_KEY
        $this->filesystem->copy($current, $prev);

        // Copy the example file so that new values are copied
        $this->filesystem->copy($dist, $current);

        $result = $this->save($config);

        // If the updated .env is the same as the backup remove the backup
        if ($this->filesystem->md5($current) === $this->filesystem->md5($prev)) {
            $this->filesystem->delete($prev);
        }

        return $result;
    }
}
