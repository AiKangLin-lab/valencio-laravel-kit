<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  UploadOptions.php
// +----------------------------------------------------------------------
// | Year:      2026/1/17/一月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\File\Upload\Options;

/**
 * 文件上传选项配置类
 */
final class UploadOptions
{
    /**
     * @param string|null $prefix 存储路径前缀，默认为'uploads'
     * @param string|null $namingStrategy 文件命名策略，默认为'sha256'
     * @param string|null $disk 存储磁盘名称，默认为'public'
     * @param string|null $appName
     * @param string|null $dateFormat
     */
    public function __construct(
        public ?string $prefix = null,
        public ?string $namingStrategy = null,
        public ?string $disk = null,
        public ?string $appName = null,
        public ?string $dateFormat = null,
    ) {
    }

    /**
     * 应用配置
     */
    public function resolve(): self
    {
        return new self(
            prefix: $this->prefix ?? config('kit.file.prefix', 'uploads'),
            namingStrategy: $this->namingStrategy ?? config('kit.file.naming_strategy', 'sha256'),
            disk: $this->disk ?? config('kit.file.disk', 'public'),
            appName: $this->appName,
            dateFormat: $this->dateFormat ?? config('kit.file.date_format', 'Ymd'),
        );
    }
}