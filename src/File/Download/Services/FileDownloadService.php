<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  FileDownloadService.php
// +----------------------------------------------------------------------
// | Year:      2026/1/17/一月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\File\Download\Services;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Valencio\LaravelKit\File\Download\Options\DownloadOptions;
use Valencio\LaravelKit\File\Storage\Registry\StorageAdapterRegistry;

/**
 * 文件下载服务
 */
readonly class FileDownloadService
{
    public function __construct(
        private StorageAdapterRegistry $adapterRegistry,
    ) {
    }

    public function download(string $path, ?DownloadOptions $options = null): StreamedResponse
    {
        $options = ($options ?? new DownloadOptions())->resolve();
        $adapter = $this->adapterRegistry->get($options->disk);
        
        return $adapter->download($path, $options->filename);
    }

    public function getDownloadUrl(string $path, ?DownloadOptions $options = null): string
    {
        $options = ($options ?? new DownloadOptions())->resolve();
        $adapter = $this->adapterRegistry->get($options->disk);
        
        return $adapter->getDownloadUrl($path);
    }

    public function exists(string $path, ?string $disk = null): bool
    {
        $disk = $disk ?? config('kit.file.disk', 'public');
        $adapter = $this->adapterRegistry->get($disk);
        
        return $adapter->exists($path);
    }
}