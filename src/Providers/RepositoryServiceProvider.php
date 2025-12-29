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

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Valencio\LaravelKit\Commands\MakeRepositoryCacheCommand;
use Valencio\LaravelKit\Commands\MakeRepositoryCommand;
use Valencio\LaravelKit\Repository\Utils\RepositoryScanner;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register (): void
    {
    }


    /**
     * 注册 Repository 接口与实现类绑定
     *
     * @return void
     */
    protected function registerRepositories (): void
    {
        $basePath = app_path('Services/Apps');
        if (!is_dir($basePath)) {
            return;
        }

        $bindings = [];

        if ($this->app->environment('production')) {
            // 生产环境优先加载静态缓存文件（部署时由 php artisan repository:cache 生成）
            $cacheFile = base_path('bootstrap/cache/repository_bindings.php');

            if (file_exists($cacheFile)) {
                $bindings = require $cacheFile;
            } else {
                $bindings = RepositoryScanner::scan($basePath);
            }
        } else {
            // 开发环境：实时扫描，避免阻塞开发体验
            $bindings = RepositoryScanner::scan($basePath);
        }

        // 统一绑定到容器
        foreach ($bindings as $interface => $repository) {
            $this->app->singleton($interface, $repository);
        }
    }

    /**
     * @return void
     */
    public function boot (): void
    {
        $this->app->booted(function() {
            $this->registerRepositories();
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeRepositoryCommand::class,
                MakeRepositoryCacheCommand::class
            ]);

            // 发布 Stub 文件
            $this->publishes([
                __DIR__ . '/../../stubs' => base_path('stubs'),
            ], 'kie-repository-stubs');
        }
    }
}
