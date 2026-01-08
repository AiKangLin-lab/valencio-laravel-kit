<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  KitServiceProvider.php
// +----------------------------------------------------------------------
// | Year:      2025/8/6/八月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\Providers;

use Illuminate\Support\ServiceProvider;
use Valencio\LaravelKit\Export\ExportManager;
use Valencio\LaravelKit\File\Adapters\PublicDiskAdapter;
use Valencio\LaravelKit\File\Adapters\StorageAdapterRegistry;
use Valencio\LaravelKit\Upload\UploadManager;

/**
 * Laravel Kit 包服务提供者
 *
 * 负责注册上传相关的配置、单例、语言包、资源发布等。
 * 支持按模块选择性发布配置文件。
 */
class KitServiceProvider extends ServiceProvider
{
    /**
     * 注册服务（合并配置、注册单例）
     *
     * @return void
     */
    public function register(): void
    {
        // 合并模块配置
        $this->mergeConfigFrom(__DIR__ . '/../../config/kit-file.php', 'kit.file');

        // 注册 UploadManager 为单例，便于依赖注入和全局调用
        $this->app->singleton(UploadManager::class, function($app) {
            return new UploadManager($app);
        });

        // 注册 ExportManager 为单例，便于依赖注入和全局调用
        $this->app->singleton(ExportManager::class, function($app) {
            return new ExportManager($app);
        });


        // 注册 registry，并注入所有适配器
        $this->app->singleton(StorageAdapterRegistry::class, function ($app) {
            return new StorageAdapterRegistry([
                $app->make(PublicDiskAdapter::class),
                // 以后加：
                // $app->make(OssDiskAdapter::class),
                // $app->make(CosDiskAdapter::class),
            ]);
        });
    }

    /**
     * 启动服务（加载语言包、发布资源）
     *
     * @return void
     */
    public function boot(): void
    {
        // 加载上传模块的语言文件，命名空间为 kit
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'kit');

        // 仅在控制台环境下发布配置和语言包
        if ($this->app->runningInConsole()) {
            // 按模块分别发布配置文件，用户可按需选择
            $this->publishes([
                __DIR__.'/../../config/kit-file.php' => config_path('kit/file.php'),
            ], 'kit-file-config');

            // 后续新增模块时，只需添加对应的 publishes 行
            // 例如：
            // $this->publishes([
            //     __DIR__.'/../../config/xx.php' => config_path('kit/xx.php'),
            // ], 'kit-xx-config');

            // 发布所有语言包（一次性发布所有语言文件）
            $this->publishes([
                __DIR__.'/../../resources/lang' => $this->app->langPath('vendor/kit'),
            ], 'kit-lang');
        }
    }
}
