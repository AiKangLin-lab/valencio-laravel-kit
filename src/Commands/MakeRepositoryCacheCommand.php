<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  MakeRepositoryCacheCommand.php
// +----------------------------------------------------------------------
// | Year:      2025/8/18/八月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\Commands;

use Illuminate\Console\Command;
use Valencio\LaravelKit\Providers\RepositoryServiceProvider;
use Illuminate\Support\Facades\File;
use Valencio\LaravelKit\Repository\Utils\RepositoryScanner;

/**
 * 生成 Repository 缓存
 */
class MakeRepositoryCacheCommand extends Command
{
    /**
     * 命令签名
     *
     * @var string
     */
    protected $signature = 'repository:cache';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '生成 Repository 接口与实现类的缓存映射';

    /**
     * 命令处理逻辑
     *
     * @return int
     */
    public function handle (): int
    {
        $bindings = RepositoryScanner::scan(app_path('Services/Apps'));

        $content = '<?php return ' . var_export($bindings, true) . ';' . PHP_EOL;

        $cachePath = base_path('bootstrap/cache/repository_bindings.php');
        File::put($cachePath, $content);

        $this->info("Repository 缓存已生成：{$cachePath}");

        return self::SUCCESS;
    }
}
