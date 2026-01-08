<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  StorageAdapterInterface.php
// +----------------------------------------------------------------------
// | Year:      2026/1/8/一月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\File\Adapters;

use Illuminate\Http\UploadedFile;
use Valencio\LaravelKit\File\FilePathResult;


/**
 * 存储适配器接口
 *
 * 定义存储操作的标准接口，用于统一不同存储方式的操作方法
 * 包括文件存储、数据库存储、缓存存储等各种存储介质的抽象
 */
interface StorageAdapterInterface
{
    /**
     * 返回当前适配器支持的 disk 名称
     * 例如：public / oss / cos
     */
    public function disk(): string;

    /**
     * 将文件保存到指定目录/文件名，返回最终保存的 key（相对路径）
     * 例如：uploads/20260108/xxxx.jpg
     */
    public function putFileAs(UploadedFile $file, FilePathResult $path): string;
}
