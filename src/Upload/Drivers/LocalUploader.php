<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  LocalUploader.php
// +----------------------------------------------------------------------
// | Year:      2025/8/6/八月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\Upload\Drivers;

use Illuminate\Http\UploadedFile;
use Valencio\LaravelKit\Upload\Contracts\Uploader;
use Valencio\LaravelKit\Upload\UploadException;

/**
 * 本地存储上传驱动
 *
 * 实现文件上传到本地磁盘，支持自定义文件名和路径。
 */
class LocalUploader implements Uploader
{
    /**
     * 驱动配置
     * @var array
     */
    protected array $config;

    /**
     * 构造函数
     * @param array $config 驱动配置
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * 存储文件到本地磁盘
     *
     * @param UploadedFile $file 待上传的文件对象
     * @param string|null $path 存储目录（可选）
     * @param string|null $filename 自定义文件名（可选）
     * @return string|false 返回存储后的相对路径或 false
     * @throws UploadException 上传失败时抛出
     */
    public function store(UploadedFile $file, ?string $path = null, ?string $filename = null): string|false
    {
        $disk = $this->config['disk'] ?? 'public';
        $prefix = $this->config['prefix'] ?? '';

        try {
            if ($filename) {
                $storedPath = $file->storeAs($path, $filename, $disk);
            } else {
                $storedPath = $file->store($path, $disk);
            }
        } catch (\Throwable $e) {
            // 统一抛出 UploadException，提示文本用语言包
            throw new UploadException(
                __('kit::upload.local_store_failed', ['msg' => $e->getMessage()]),
                0,
                $e
            );
        }

        if ($storedPath === false) {
            throw new UploadException(__('kit::upload.local_store_failed', ['msg' => 'store 返回 false']));
        }

        // 如果有配置前缀，则拼接前缀
        if (!empty($prefix)) {
            return rtrim($prefix, '/') . '/' . ltrim($storedPath, '/');
        }

        return $storedPath;
    }
}
