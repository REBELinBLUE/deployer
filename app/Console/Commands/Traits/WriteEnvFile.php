<?php

namespace REBELinBLUE\Deployer\Console\Commands\Traits;

// FIXME: Move this to a class?
trait WriteEnvFile
{
    /**
     * Writes the configuration data to the config file.
     *
     * @param array $input The config data to write
     *
     * @return bool
     */
    protected function writeEnvFile(array $input)
    {
        $this->info('Writing configuration file');

        $path   = base_path('.env');
        $config = $this->filesystem->get($path);

        // Move the socket value to the correct key
        if (isset($input['app']['socket'])) {
            $input['socket']['url'] = $input['app']['socket'];
            unset($input['app']['socket']);
        }

        if (isset($input['app']['ssl'])) {
            foreach ($input['app']['ssl'] as $key => $value) {
                $input['socket']['ssl_' . $key] = $value;
            }

            unset($input['app']['ssl']);
        }

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
            foreach (['host', 'database', 'username', 'password'] as $key) {
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
}
