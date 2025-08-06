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
use Illuminate\Support\Facades\Log;
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
            Log::error('上传驱动未配置', ['driver' => $name]);
            throw new UploadException(__('kit::upload.driver_not_configured', ['driver' => $name]));
        }

        // 根据驱动名创建对应的驱动实例
        $methodName = 'create' . ucfirst($name) . 'Driver';
        if (method_exists($this, $methodName)) {
            return $this->$methodName($config);
        }

        Log::error('上传驱动不支持', ['driver' => $name]);
        throw new UploadException(__('kit::upload.driver_not_supported', ['driver' => $name]));
    }

    /**
     * 存储文件
     * @param UploadedFile $file 待上传的文件对象
     * @param string|null $path 存储目录（可选）
     * @param string $ruleset 验证规则集
     * @param string|null $filename 自定义文件名（可选）
     * @return string|false
     * @throws UploadException
     */
    public function store(UploadedFile $file, ?string $path = null, string $ruleset = 'default', ?string $filename = null): string|false
    {
        // 1. 确保驱动已被选择，如果没有，使用默认驱动
        if (!$this->currentDriver) {
            $this->driver();
        }

        // 2. 执行验证
        $this->validate($file, $ruleset);

        // 3. 统一命名策略
        if ($filename === null) {
            $naming = $this->app['config']['kit.upload.naming'] ?? 'random';
            if ($naming === 'md5') {
                $filename = md5_file($file->getRealPath()) . '.' . $file->getClientOriginalExtension();
            } elseif ($naming === 'sha1') {
                $filename = sha1_file($file->getRealPath()) . '.' . $file->getClientOriginalExtension();
            } else {
                $filename = null; // 让驱动自己用默认
            }
        }

        // 4. 调用驱动进行存储
        try {
            return $this->currentDriver->store($file, $path, $filename);
        } catch (\Throwable $e) {
            Log::error('文件上传失败', [
                'file' => $file->getClientOriginalName(),
                'path' => $path,
                'filename' => $filename,
                'exception' => $e
            ]);
            throw new UploadException(
                __('kit::upload.upload_failed', ['msg' => $e->getMessage()]),
                0,
                $e
            );
        }
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
            Log::warning('上传验证规则集未找到', ['ruleset' => $ruleset]);
            return;
        }

        // 实例化并执行验证
        try {
            (new Validator())->execute($file, $rules);
        } catch (\Throwable $e) {
            Log::error('文件验证失败', [
                'file' => $file->getClientOriginalName(),
                'ruleset' => $ruleset,
                'exception' => $e
            ]);
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
