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
     * @param string $prefix 存储路径前缀，默认为'uploads'
     * @param string $namingStrategy 文件命名策略，默认为'sha256'
     * @param string $disk 存储磁盘名称，默认为'public'
     * @return FileUploadResult 文件上传结果对象
     * @throws RandomException
     */
    public function store (
        UploadedFile $file,
        string       $prefix = 'uploads',
        string       $namingStrategy = 'sha256',
        string       $disk = 'public'
    ): FileUploadResult {

        print_r(config('kit.file'));
        exit;
        // 生成文件存储路径
        $pathResult = $this->pathGenerator->generate(
            file: $file,
            prefix: $prefix,
            dateFormat: 'Ymd',
            namingStrategy: $namingStrategy
        );

        // 获取指定磁盘的适配器
        $adapter = $this->adapterRegistry->get($disk);

        try {
            $key = $adapter->putFileAs($file, $pathResult);
        } catch (RuntimeException $e) {
            throw new RuntimeException($e->getMessage());
        }

        return new FileUploadResult(
            disk: $disk,
            path: $key,
            url: Storage::disk($disk)->url($key),
        );
    }

}
