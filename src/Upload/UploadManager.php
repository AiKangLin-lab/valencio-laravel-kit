<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  UploadManager.php
// +----------------------------------------------------------------------
// | Year:      2025/8/6/八月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\Upload;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Valencio\LaravelKit\Upload\Contracts\Uploader;
use Valencio\LaravelKit\Upload\Drivers\LocalUploader;

/**
 * 上传管理器
 *
 * 负责上传驱动的解析、命名策略、统一验证、异常处理等。
 */
class UploadManager
{
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;
    /**
     * @var array
     */
    protected $drivers = [];
    /**
     * @var Uploader|null
     */
    protected $currentDriver;

    /**
     * 构造函数
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * 选择一个驱动并返回管理器实例，以支持链式调用。
     * @param string|null $name 驱动名称
     * @return $this
     */
    public function driver(?string $name = null): self
    {
        $driverName = $name ?: $this->getDefaultDriver();
        $this->currentDriver = $this->resolve($driverName);
        return $this;
    }

    /**
     * 解析驱动实例
     * @param string $name 驱动名称
     * @return Uploader
     * @throws UploadException
     */
    protected function resolve(string $name): Uploader
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new UploadException(__('kit::upload.driver_not_configured', ['driver' => $name]));
        }

        // 根据驱动名创建对应的驱动实例
        $methodName = 'create' . ucfirst($name) . 'Driver';
        if (method_exists($this, $methodName)) {
            return $this->$methodName($config);
        }

        throw new UploadException(__('kit::upload.driver_not_supported', ['driver' => $name]));
    }

    /**
     * 存储文件
     * @param UploadedFile $file 待上传的文件对象
     * @param string|null $path 存储目录（可选，优先级高于 storage_prefix）
     * @param string $ruleset 验证规则集
     * @param string|null $filename 自定义文件名（可选）
     * @return string|false
     * @throws UploadException
     */
    public function store(UploadedFile $file, ?string $path = null, string $ruleset = 'default', ?string $filename = null): string|false
    {
        if (!$this->currentDriver) {
            $this->driver();
        }
        $this->validate($file, $ruleset);

        $dir = $this->buildStorageDir($path);
        $finalFileName = $this->buildFileName($file, $filename);

        $storedPath = $this->currentDriver->store($file, $dir, $finalFileName);
        if ($storedPath === false) {
            throw new UploadException(__('kit::upload.upload_failed', ['msg' => 'store 返回 false']));
        }

        $accessPrefix = $this->getAccessPrefix();
        $dateDir = basename($dir); // 目录名为日期
        $finalName = $finalFileName ?? basename($storedPath);
        return rtrim($accessPrefix, '/') . '/' . $dateDir . '/' . $finalName;
    }

    /**
     * 生成存储目录
     * @param string|null $path
     * @return string
     */
    protected function buildStorageDir(?string $path): string
    {
        if ($path) return $path;
        $storagePrefix = $this->app['config']['kit.upload.storage_prefix'] ?? 'uploads';
        return $storagePrefix . '/' . date('Ymd');
    }

    /**
     * 生成文件名
     * @param UploadedFile $file
     * @param string|null $filename
     * @return string|null
     */
    protected function buildFileName(\Illuminate\Http\UploadedFile $file, ?string $filename): ?string
    {
        if ($filename !== null) return $filename;
        $naming = $this->app['config']['kit.upload.naming'] ?? 'random';
        $ext = $file->getClientOriginalExtension();
        if ($naming === 'md5') {
            return md5_file($file->getRealPath() . time()) . '.' . $ext;
        } elseif ($naming === 'sha1') {
            return sha1_file($file->getRealPath() . time()) . '.' . $ext;
        }
        return null; // 让驱动自己用默认
    }

    /**
     * 获取访问前缀
     * @return string
     */
    protected function getAccessPrefix(): string
    {
        $driverName = $this->currentDriver instanceof \Valencio\LaravelKit\Upload\Drivers\LocalUploader ? 'local' : null;
        $driversConfig = $this->app['config']['kit.upload.drivers'] ?? [];
        return $driversConfig[$driverName]['access_prefix'] ?? '/storage/uploads';
    }

    /**
     * 创建本地驱动实例
     * @param array $config
     * @return Uploader
     */
    protected function createLocalDriver(array $config): Uploader
    {
        return new LocalUploader($config);
    }

    /**
     * 执行上传文件的验证
     * @param UploadedFile $file
     * @param string $ruleset
     * @return void
     * @throws UploadException
     */
    protected function validate(UploadedFile $file, string $ruleset): void
    {
        $config = $this->app['config']['kit.upload.validation'];

        // 如果配置中禁用了验证，则直接返回
        if (empty($config['enabled'])) {
            return;
        }

        // 从配置中获取对应场景的规则
        $rules = $config['rulesets'][$ruleset] ?? null;

        if (empty($rules)) {
            // 如果找不到指定的规则集，可以抛出异常或直接忽略
            return;
        }

        // 实例化并执行验证
        try {
            (new Validator())->execute($file, $rules);
        } catch (\Throwable $e) {
            throw new UploadException(
                __('kit::upload.validation_failed', ['msg' => $e->getMessage()]),
                0,
                $e
            );
        }
    }

    /**
     * 获取默认驱动名称
     * @return string
     */
    public function getDefaultDriver(): string
    {
        return $this->app['config']['kit.upload.default']->value;
    }

    /**
     * 获取驱动配置
     * @param string $name
     * @return array|null
     */
    protected function getConfig(string $name): ?array
    {
        return Arr::get($this->app['config']['kit.upload.drivers'], $name);
    }
}
