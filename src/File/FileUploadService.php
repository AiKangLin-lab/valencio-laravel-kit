<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  FileUploadService.php
// +----------------------------------------------------------------------
// | Year:      2026/1/8/一月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\File;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Random\RandomException;
use RuntimeException;
use Valencio\LaravelKit\File\Adapters\StorageAdapterRegistry;


/**
 * 文件上传服务类
 *
 * 该只读类提供文件上传相关的服务功能
 * 使用readonly关键字确保类的不可变性
 */
readonly class FileUploadService
{
    /**
     * FileUploadService constructor.
     *
     * @param FilePathGenerator $pathGenerator 文件路径生成器对象
     * @param StorageAdapterRegistry $adapterRegistry 存储适配器注册对象
     */
    public function __construct (
        private FilePathGenerator      $pathGenerator,
        private StorageAdapterRegistry $adapterRegistry,
    ) {
    }


    /**
     * 存储上传的文件到指定磁盘
     *
     * @param UploadedFile $file 要存储的上传文件对象
     * @return FileUploadResult 文件上传结果对象
     * @throws RandomException
     */
    public function store (
        UploadedFile   $file,
        ?UploadOptions $options = null
    ): FileUploadResult {
        $options = ($options ?? new UploadOptions())->resolve();

        // 生成文件存储路径
        $pathResult = $this->pathGenerator->generate(
            file: $file,
            appName: $options->appName,
            prefix: $options->prefix,
            dateFormat: $options->dateFormat,
            namingStrategy: $options->namingStrategy
        );

        // 获取指定磁盘的适配器
        $adapter = $this->adapterRegistry->get($options->disk);

        try {
            $key = $adapter->putFileAs($file, $pathResult);
        } catch (RuntimeException $e) {
            throw new RuntimeException($e->getMessage());
        }

        return new FileUploadResult(
            disk: $options->disk,
            path: $key,
            url: Storage::disk($options->disk)->url($key)
        );
    }

}
