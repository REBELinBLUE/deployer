<?php

namespace REBELinBLUE\Deployer\Jobs\DeployProject;

use Exception;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\ConfigFile;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\DeployStep;
use REBELinBLUE\Deployer\Server;
use REBELinBLUE\Deployer\Services\Scripts\Parser as ScriptParser;
use REBELinBLUE\Deployer\Services\Scripts\Runner as Process;
use REBELinBLUE\Deployer\SharedFile;

/**
 * Class to generate tokens for scripts.
 */
class ScriptBuilder
{
    /**
     * @var ScriptParser
     */
    private $parser;

    /**
     * @var Process
     */
    private $process;

    /**
     * @var Deployment
     */
    private $deployment;

    /**
     * @var string
     */
    private $private_key;

    /**
     * @var string
     */
    private $release_archive;

    /**
     * @var DeployStep
     */
    private $step;

    /**
     * ScriptBuilder constructor.
     *
     * @param Process      $process
     * @param ScriptParser $parser
     */
    public function __construct(Process $process, ScriptParser $parser)
    {
        $this->process = $process;
        $this->parser  = $parser;
    }

    /**
     * @param  Deployment $deployment
     * @param  DeployStep $step
     * @param  string     $release_archive
     * @param  string     $private_key
     * @return $this
     */
    public function setup(Deployment $deployment, DeployStep $step, $release_archive, $private_key)
    {
        $this->deployment      = $deployment;
        $this->step            = $step;
        $this->release_archive = $release_archive;
        $this->private_key     = $private_key;

        return $this;
    }

    /**
     * Generates the actual bash commands to run on the server.
     *
     * @param Server $server
     *
     * @throws Exception
     * @return Process
     */
    public function buildScript(Server $server)
    {
        if (!isset($this->deployment)) {
            throw new Exception('Setup has not been called');
        }

        $tokens = $this->getTokens($server);

        $user = $server->user;
        if ($this->step->isCustom()) {
            $user = empty($this->step->command->user) ? $server->user : $this->step->command->user;
        }

        // Now get the full script
        return $this->getScriptForStep($tokens)
                    ->prependScript($this->exports())
                    ->setServer($server, $this->private_key, $user);
    }

    /**
     * Generates the script for exports.
     *
     * @return string
     */
    private function exports()
    {
        // Generate the export
        $exports = '';

        /** @var Collection $variables */
        $variables = $this->deployment->project->variables;
        $variables->each(function ($variable) use (&$exports) {
            $key   = $variable->name;
            $value = $variable->value;

            $exports .= "export {$key}={$value}" . PHP_EOL;
        });

        return $exports;
    }

    /**
     * Generates the list of tokens for the scripts.
     *
     * @param Server $server
     *
     * @return array
     */
    private function getTokens(Server $server)
    {
        $releases_dir       = $server->clean_path . '/releases';
        $latest_release_dir = $releases_dir . '/' . $this->deployment->release_id;
        $release_shared_dir = $server->clean_path . '/shared';
        $remote_archive     = $server->clean_path . '/' . $this->release_archive;

        // Set the deployer tags
        $deployer_email = '';
        $deployer_name  = 'webhook';
        if ($this->deployment->user) {
            $deployer_name  = $this->deployment->user->name;
            $deployer_email = $this->deployment->user->email;
        } elseif ($this->deployment->is_webhook && !empty($this->deployment->source)) {
            $deployer_name = $this->deployment->source;
        }

        $tokens = [
            'release'         => $this->deployment->release_id,
            'deployment'      => $this->deployment->id,
            'release_path'    => $latest_release_dir,
            'project_path'    => $server->clean_path,
            'branch'          => $this->deployment->branch,
            'sha'             => $this->deployment->commit,
            'short_sha'       => $this->deployment->short_commit,
            'deployer_email'  => $deployer_email,
            'deployer_name'   => $deployer_name,
            'committer_email' => $this->deployment->committer_email,
            'committer_name'  => $this->deployment->committer,
        ];

        if (!$this->step->isCustom()) {
            $tokens = array_merge($tokens, [
                'remote_archive' => $remote_archive,
                'include_dev'    => $this->deployment->project->include_dev,
                'builds_to_keep' => $this->deployment->project->builds_to_keep + 1,
                'shared_path'    => $release_shared_dir,
                'releases_path'  => $releases_dir,
            ]);
        }

        return $tokens;
    }

    /**
     * Gets the process which is used for the supplied step.
     *
     * @param array $tokens
     *
     * @return Process
     */
    private function getScriptForStep(array $tokens = [])
    {
        // FIXME: Prepend create directories
        switch ($this->step->stage) {
            case Command::DO_CLONE:
                return $this->process->setScript('deploy.steps.CreateNewRelease', $tokens);
            case Command::DO_INSTALL:
                $release_path = $tokens['release_path'];
                $shared_path  = $tokens['shared_path'];
                $project_path = $tokens['project_path'];

                // Write configuration file to release dir, symlink shared files and run composer
                return $this->process
                            ->setScript('deploy.steps.InstallComposerDependencies', $tokens)
                            ->prependScript($this->configurationFileCommands($release_path))
                            ->appendScript($this->shareFileCommands($release_path, $shared_path, $project_path));
            case Command::DO_ACTIVATE:
                return $this->process->setScript('deploy.steps.ActivateNewRelease', $tokens);
            case Command::DO_PURGE:
                return $this->process->setScript('deploy.steps.PurgeOldReleases', $tokens);
        }

        // Custom step
        $script = '### Custom script - {{ deployment }}' . PHP_EOL . $this->step->command->script;

        return $this->process->setScript($script, $tokens, Process::DIRECT_INPUT);
    }

    /**
     * create the command for sending uploaded files.
     *
     * @param string $release_dir
     *
     * @return string
     */
    private function configurationFileCommands($release_dir)
    {
        /** @var Collection $files */
        $files = $this->deployment->project->configFiles;
        if (!$files->count()) {
            return '';
        }

        $script = '';
        $files->each(function (ConfigFile $file) use (&$script, $release_dir) {
            $script .= $this->parser->parseFile('deploy.ConfigurationFile', [
                'deployment' => $this->deployment->id,
                'path'       => $release_dir . '/' . $file->path,
            ]);
        });

        return $script . PHP_EOL;
    }

    /**
     * Create the script for shared files.
     *
     * @param string $release_dir
     * @param string $shared_dir
     * @param string $project_dir
     *
     * @return string
     */
    private function shareFileCommands($release_dir, $shared_dir, $project_dir)
    {
        /** @var Collection $files */
        $files = $this->deployment->project->sharedFiles;
        if (!$files->count()) {
            return '';
        }

        $migration = '.deployer-migrated';
        $backup_dir = $shared_dir . '.backup';

        $script = $this->parser->parseFile('deploy.MigrateShared', [
            'deployment'  => $this->deployment->id,
            'shared_dir'  => $shared_dir,
            'project_dir' => $project_dir,
            'backup_dir'  => $backup_dir,
            'migration'   => $migration,
        ]);

        $files->each(function (SharedFile $shared) use (&$script, $release_dir, $shared_dir, $backup_dir) {
            $pathinfo = pathinfo($shared->file);
            $template = 'File';

            $file = ltrim($shared->file, '/1');

            if (ends_with($file, '/')) {
                $template = 'Directory';
                $file = rtrim($file, '/');
            }

            $script .= $this->parser->parseFile('deploy.Share' . $template, [
                'deployment'  => $this->deployment->id,
                'shared_dir'  => $shared_dir,
                'release_dir' => $release_dir,
                'backup_dir'  => $backup_dir,
                'path'        => $file,
                'filename'    => $pathinfo['basename'],
                'parent_dir'  => ltrim($pathinfo['dirname'], '/'),
            ]);
        });

        $script .= $this->parser->parseFile('deploy.MigrateSharedTimestamp', [
            'migration'  => $migration,
            'shared_dir' => $shared_dir,
            'release'    => $this->deployment->release_id,
        ]);

        return PHP_EOL . $script;
    }
}
