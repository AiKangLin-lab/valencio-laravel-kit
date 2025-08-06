<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  Uploader.php
// +----------------------------------------------------------------------
// | Year:      2025/8/6/八月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\Upload\Contracts;

use Illuminate\Http\UploadedFile;

/**
 * 上传驱动统一接口
 *
 * 所有上传驱动都必须实现本接口，便于扩展和解耦。
 */
interface Uploader
{
    /**
     * 存储文件。
     *
     * @param UploadedFile $file 待上传的文件对象
     * @param string|null $path 存储目录（可选）
     * @param string|null $filename 自定义文件名（可选）
     * @return string|false 返回存储后的相对路径或 false
     * @throws \Valencio\LaravelKit\Upload\UploadException 上传失败时抛出
     */
    public function store(UploadedFile $file, ?string $path = null, ?string $filename = null): string|false;
}
