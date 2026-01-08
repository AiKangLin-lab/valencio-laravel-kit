<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  FileUploadResult.php
// +----------------------------------------------------------------------
// | Year:      2026/1/8/一月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\File;

/**
 * 文件上传结果（核心层返回值对象）
 */
final readonly class FileUploadResult
{
    public function __construct(
        public string $disk,
        public string $path, // 存库用：uploads/20260108/xxx.jpg
        public string $url,  // 展示用：/storage/uploads/...
    ) {}
}
