<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  RepositoryServiceProvider.php
// +----------------------------------------------------------------------
// | Year:      2025/8/14/八月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->registerRepositories();
    }


    protected function registerRepositories()
    {
        $basePath = app_path('Services');
        if (!is_dir($basePath)) {
            return;
        }

        $modules = array_diff(scandir($basePath), ['.', '..']);
        foreach ($modules as $module) {
            $modulePath = $basePath . '/' . $module;
            if (!is_dir($modulePath)) {
                continue;
            }

            $entities = array_diff(scandir($modulePath), ['.', '..']);
            foreach ($entities as $entity) {
                $interface = "App\\Services\\{$module}\\{$entity}\\Contracts\\{$entity}RepositoryInterface";
                $repository = "App\\Services\\{$module}\\{$entity}\\Repository\\{$entity}Repository";
                if (interface_exists($interface) && class_exists($repository)) {
                    $this->app->singleton($interface, $repository);
                }
            }
        }
    }
}
