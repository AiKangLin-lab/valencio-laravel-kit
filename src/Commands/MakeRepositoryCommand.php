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
    protected $signature = 'make:repository {name : The name of the repository (e.g. Ai/Chat/ConversationUsageLedger)} {--module=Apps : The module name (e.g., Apps, Core, Integration)} {--flat : 在已有目录下生成，不创建新的子目录（仅生成接口与实现类到现有 Contracts/Repository）}';

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
     * @param string $path 目录路径（flat 时为父路径如 Ai/Chat，否则为完整路径如 Ai/Chat/ConversationUsageLedger）
     * @param string $module
     * @param string|null $className flat 模式下显式传入类名，否则从 path 取 basename
     * @return string
     */
    protected function getInterfacePath (string $path, string $module, ?string $className = null): string
    {
        $path = str_replace('\\', '/', $path);
        $className = $className ?? basename($path);
        return app_path("Services/{$module}/{$path}/Contracts/{$className}RepositoryInterface.php");
    }

    /**
     * Get the destination path for the repository.
     *
     * @param string $path
     * @param string $module
     * @param string|null $className
     * @return string
     */
    protected function getRepositoryPath (string $path, string $module, ?string $className = null): string
    {
        $path = str_replace('\\', '/', $path);
        $className = $className ?? basename($path);
        return app_path("Services/{$module}/{$path}/Repository/{$className}Repository.php");
    }


    /**
     * @return void
     * @throws FileNotFoundException
     */
    public function handle (): void
    {
        $name = $this->argument('name');
        $name = str_replace('\\', '/', $name);
        $segments = array_map(fn($s) => Str::studly($s), explode('/', $name));
        $name = implode('/', $segments);

        $module = Str::studly($this->option('module'));
        $flat = $this->option('flat');

        if ($flat) {
            // 在已有目录下生成：父路径 + 类名，不新建子目录
            $basePath = dirname($name);
            $className = basename($name);
            if ($basePath === '.' || $basePath === '') {
                $this->error('使用 --flat 时 name 必须包含路径，例如：Ai/Chat/ConversationUsageLedger');
                return;
            }
            $modulePath = app_path("Services/{$module}/{$basePath}");
            $this->files->ensureDirectoryExists("{$modulePath}/Contracts");
            $this->files->ensureDirectoryExists("{$modulePath}/Repository");
            $this->createInterface($basePath, $module, true, $className);
            $this->createRepository($basePath, $module, true, $className);
            $this->info("Repository [{$className}] 已生成到 {$module}/{$basePath} 的 Contracts 与 Repository 目录。");
        } else {
            // 原有逻辑：为 name 创建子目录 Contracts / Repository
            $modulePath = app_path("Services/{$module}/{$name}");
            $this->files->ensureDirectoryExists("{$modulePath}/Contracts");
            $this->files->ensureDirectoryExists("{$modulePath}/Repository");
            $this->createInterface($name, $module, false);
            $this->createRepository($name, $module, false);
            $this->info("Repository for {$name} created successfully in {$module} module.");
        }
    }


    /**
     * Create the repository interface.
     *
     * @param string $path 目录路径（flat 时为父路径，否则为完整路径）
     * @param string $module
     * @param bool $flat 是否 flat 模式（在已有目录下生成）
     * @param string|null $className flat 时传入类名，否则从 path 取 basename
     * @return void
     * @throws FileNotFoundException
     */
    protected function createInterface (string $path, string $module, bool $flat = false, ?string $className = null): void
    {
        $path = str_replace('\\', '/', $path);
        $className = $className ?? basename($path);
        $pathNamespace = str_replace('/', '\\', $path);
        $namespace = "App\\Services\\{$module}\\{$pathNamespace}\\Contracts";
        $modelNamespace = $flat
            ? "App\\Models\\{$pathNamespace}\\{$className}"
            : "App\\Models\\{$pathNamespace}";

        $stub = $this->files->get($this->getStub()['interface']);
        $stub = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ model }}'],
            [$namespace, $className, $modelNamespace],
            $stub
        );

        $this->files->put($this->getInterfacePath($path, $module, $className), $stub);
    }


    /**
     * Create the repository implementation.
     *
     * @param string $path 目录路径（flat 时为父路径，否则为完整路径）
     * @param string $module
     * @param bool $flat 是否 flat 模式（在已有目录下生成）
     * @param string|null $className flat 时传入类名，否则从 path 取 basename
     * @return void
     * @throws FileNotFoundException
     */
    protected function createRepository (string $path, string $module, bool $flat = false, ?string $className = null): void
    {
        $path = str_replace('\\', '/', $path);
        $className = $className ?? basename($path);
        $pathNamespace = str_replace('/', '\\', $path);
        $namespace = "App\\Services\\{$module}\\{$pathNamespace}\\Repository";
        $interfaceNamespace = "App\\Services\\{$module}\\{$pathNamespace}\\Contracts\\{$className}RepositoryInterface";
        $modelNamespace = $flat
            ? "App\\Models\\{$pathNamespace}\\{$className}"
            : "App\\Models\\{$pathNamespace}";

        $stub = $this->files->get($this->getStub()['repository']);
        $stub = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ interface }}', '{{ model }}'],
            [$namespace, $className, $interfaceNamespace, $modelNamespace],
            $stub
        );
        $this->files->put($this->getRepositoryPath($path, $module, $className), $stub);
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
            ['flat', null, InputOption::VALUE_NONE, '在已有目录下生成，不创建新子目录（仅生成接口与实现类到现有 Contracts/Repository）'],
            ['test', null, InputOption::VALUE_NONE, 'Generate a test file'],
        ];
    }
}
