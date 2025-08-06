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
     * @param string|null $path 存储目录（如 uploads/20251012）
     * @param string|null $filename 自定义文件名（可选）
     * @return string|false 返回存储后的相对路径或 false
     * @throws UploadException 上传失败时抛出
     */
    public function store(UploadedFile $file, ?string $path = null, ?string $filename = null): string|false
    {
        $disk = $this->config['disk'] ?? 'public';
        try {
            if ($filename) {
                $storedPath = $file->storeAs($path, $filename, $disk);
            } else {
                $storedPath = $file->store($path, $disk);
            }
        } catch (\Throwable $e) {
            throw new UploadException(
                __('kit::upload.local_store_failed', ['msg' => $e->getMessage()]),
                0,
                $e
            );
        }
        if ($storedPath === false) {
            throw new UploadException(__('kit::upload.local_store_failed', ['msg' => 'store 返回 false']));
        }
        return $storedPath;
    }
}
