<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  FileUploadService.php
// +----------------------------------------------------------------------
// | Year:      2026/1/17/一月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\File\Upload\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Random\RandomException;
use Throwable;
use Valencio\LaravelKit\File\Core\Results\FileUploadResult;
use Valencio\LaravelKit\File\Exceptions\FileException;
use Valencio\LaravelKit\File\Storage\Registry\StorageAdapterRegistry;
use Valencio\LaravelKit\File\Upload\Generators\FilePathGenerator;
use Valencio\LaravelKit\File\Upload\Options\UploadOptions;

/**
 * 文件上传服务
 */
readonly class FileUploadService
{
    public function __construct (
        private FilePathGenerator      $pathGenerator,
        private StorageAdapterRegistry $adapterRegistry,
    ) {
    }

    /**
     * @param UploadedFile $file
     * @param UploadOptions|null $options
     * @return FileUploadResult
     * @throws FileException
     * @throws RandomException
     */
    public function store (UploadedFile $file, ?UploadOptions $options = null): FileUploadResult
    {
        $options = ($options ?? new UploadOptions())->resolve();

        $pathResult = $this->pathGenerator->generate(
            file: $file,
            appName: $options->appName,
            prefix: $options->prefix,
            dateFormat: $options->dateFormat,
            namingStrategy: $options->namingStrategy
        );

        $adapter = $this->adapterRegistry->get($options->disk);

        try {
            $key = $adapter->putFileAs($file, $pathResult);
        } catch (Throwable $e) {
            throw new FileException($e->getMessage());
        }

        return new FileUploadResult(
            disk: $options->disk,
            path: $key,
            url: Storage::disk($options->disk)->url($key)
        );
    }
}
