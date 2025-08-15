<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  MakeRepositoryCommand.php
// +----------------------------------------------------------------------
// | Year:      2025/8/14/八月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

/**
 *
 */
class MakeRepositoryCommand extends GeneratorCommand
{

    /**
     * @var string
     */
    protected $signature = 'make:repository {name : The name of the model} {--module=Apps : The module name (e.g., Apps, Core, Integration)}';

    /**
     * @var string
     */
    protected $description = 'Create a new repository interface, implementation, and optional test file';

    /**
     * @var string
     */
    protected $type = 'Repository';


    /**
     * Get the stub files for interface and repository.
     *
     * @return array
     */
    protected function getStub (): array
    {
        return [
            'interface' => __DIR__ . '/../../stubs/repository.interface.stub',
            'repository' => __DIR__ . '/../../stubs/repository.stub',
        ];
    }


    /**
     * Get the destination path for the interface.
     *
     * @param string $name
     * @param string $module
     * @return string
     */
    protected function getInterfacePath (string $name, string $module): string
    {
        return app_path("Services/{$module}/{$name}/Contracts/{$name}RepositoryInterface.php");
    }

    /**
     * Get the destination path for the repository.
     *
     * @param string $name
     * @param string $module
     * @return string
     */
    protected function getRepositoryPath (string $name, string $module): string
    {
        return app_path("Services/{$module}/{$name}/Repository/{$name}Repository.php");
    }


    /**
     * @return void
     * @throws FileNotFoundException
     */
    public function handle (): void
    {

        $name = Str::studly($this->argument('name'));
        $module = Str::studly($this->option('module'));
        //

        // Ensure module directory exists
        $modulePath = app_path("Services/{$module}/{$name}");
        $this->files->ensureDirectoryExists("{$modulePath}/Contracts");
        $this->files->ensureDirectoryExists("{$modulePath}/Repository");


        // Generate Interface
        $this->createInterface($name, $module);

        $this->createRepository($name, $module);


        $this->info("Repository for {$name} created successfully in {$module} module.");
    }


    /**
     * Create the repository interface.
     *
     * @param string $name
     * @param string $module
     * @return void
     * @throws FileNotFoundException
     */
    protected function createInterface (string $name, string $module): void
    {
        $stub = $this->files->get($this->getStub()['interface']);
        $namespace = "App\\Services\\{$module}\\{$name}\\Contracts";
        $stub = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ model }}'],
            [$namespace, $name, "App\\Models\\{$name}"],
            $stub
        );

        $this->files->put($this->getInterfacePath($name, $module), $stub);
    }


    /**
     * Create the repository implementation.
     *
     * @param string $name
     * @param string $module
     * @return void
     * @throws FileNotFoundException
     */
    protected function createRepository (string $name, string $module): void
    {
        $stub = $this->files->get($this->getStub()['repository']);
        $namespace = "App\\Services\\{$module}\\{$name}\\Repository";
        $interfaceNamespace = "App\\Services\\{$module}\\{$name}\\Contracts\\{$name}RepositoryInterface";
        $modelNamespace = "App\\Models\\{$name}";
        $stub = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ interface }}', '{{ model }}'],
            [$namespace, $name, $interfaceNamespace, $modelNamespace],
            $stub
        );
        $this->files->put($this->getRepositoryPath($name, $module), $stub);
    }


    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions (): array
    {
        return [
            ['module', null, InputOption::VALUE_REQUIRED, 'The module name (e.g., Apps, Core, Integration)', 'Apps'],
            ['test', null, InputOption::VALUE_NONE, 'Generate a test file'],
        ];
    }
}
