<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  StorageAdapterInterface.php
// +----------------------------------------------------------------------
// | Year:      2026/1/17/一月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\File\Core\Contracts;

use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Valencio\LaravelKit\File\Core\Results\FilePathResult;

/**
 * 存储适配器接口
 */
interface StorageAdapterInterface
{
    /**
     * 返回当前适配器支持的 disk 名称
     */
    public function disk (): string;

    /**
     * 上传文件
     */
    public function putFileAs (UploadedFile $file, FilePathResult $path): string;

    /**
     * 下载文件
     */
    public function download (string $path, ?string $filename = null): StreamedResponse;


    /**
     * 检查文件是否存在
     */
    public function exists (string $path): bool;
}
