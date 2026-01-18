<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  DownloadOptions.php
// +----------------------------------------------------------------------
// | Year:      2026/1/17/一月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\File\Download\Options;

/**
 * 文件下载选项
 */
final class DownloadOptions
{
    /**
     * @param string|null $disk
     * @param string|null $filename
     */
    public function __construct (
        public ?string $disk = null,
        public ?string $filename = null,
    ) {
    }

    /**
     * @return self
     */
    public function resolve (): self
    {
        return new self(
            disk: $this->disk ?? config('kit.file.disk', 'public'),
            filename: $this->filename,
        );
    }
}
