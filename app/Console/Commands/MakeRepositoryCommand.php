<?php

namespace REBELinBLUE\Deployer\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Support\Composer;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;

/**
 * A command to make repositories.
 * @todo Split into package
 */
class MakeRepositoryCommand extends Command
{
    use DetectsApplicationNamespace;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {name : The name for the repository}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository';

    /**
     * Meta information for the requested migration.
     *
     * @var array
     */
    protected $meta;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Composer
     */
    private $composer;

    /**
     * Create a new command instance.
     *
     * @param Filesystem $filesystem
     * @param Composer   $composer
     */
    public function __construct(Filesystem $filesystem, Composer $composer)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
        $this->composer   = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return int Exit status code
     */
    public function handle()
    {
        $name = $this->argument('name');

        $app = $this->getAppNamespace();

        $meta = [
            'name'       => $name,
            'namespaces' => [
                'contract'   => $app . 'Repositories\\Contracts',
                'repository' => $app . 'Repositories',
            ],
            'filenames' => [
                'contract'   => 'Repositories/Contracts/' . $name . 'RepositoryInterface.php',
                'repository' => 'Repositories/Eloquent' . $name . 'Repository.php',
            ],
        ];

        return $this->makeRepository($meta);
    }

    /**
     * Generate the desired repository.
     *
     * @param  array $meta
     * @return int   Exit status
     */
    protected function makeRepository(array $meta)
    {
        foreach ($meta['filenames'] as $path) {
            if ($this->filesystem->exists(app_path($path))) {
                $this->error($path . ' already exists!');

                return -1;
            }
        }

        $this->createContract($meta['namespaces']['contract'], $meta['name'], $meta['filenames']['contract']);
        $this->createConcrete($meta['namespaces']['repository'], $meta['name'], $meta['filenames']['repository']);

        $this->composer->dumpAutoloads();

        return 0;
    }

    /**
     * Create the contract.
     *
     * @param string $namespace
     * @param string $name
     * @param string $filename
     */
    private function createContract($namespace, $name, $filename)
    {
        $contract  = $name . 'RepositoryInterface';

        $content = <<< EOF
<?php

namespace $namespace;

interface $contract
{

}

EOF;

        $this->createFile($filename, $content);
    }

    /**
     * Create the concrete implementation.
     * @param string $namespace
     * @param string $name
     * @param string $filename
     */
    private function createConcrete($namespace, $name, $filename)
    {
        $interface  = $name . 'RepositoryInterface';
        $repository = 'Eloquent' . $name . 'Repository';

        $content = <<< EOF
<?php

namespace $namespace;

use $namespace\\Contracts\\$interface;

class $repository extends EloquentRepository implements $interface
{

}

EOF;

        $this->createFile($filename, $content);
    }

    /**
     * @param string $filename
     * @param string $content
     */
    private function createFile($filename, $content)
    {
        $this->filesystem->put(app_path($filename), $content);
        $this->info($filename . ' created successfully.');
    }
}
