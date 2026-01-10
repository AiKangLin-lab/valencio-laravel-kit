<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  OSSDiskAdapter.php
// +----------------------------------------------------------------------
// | Year:      2026/1/9/一月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\File\Adapters;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Valencio\LaravelKit\File\Exceptions\FileException;
use Valencio\LaravelKit\File\FilePathResult;

class OSSDiskAdapter implements StorageAdapterInterface
{
    /**
     * 获取存储的磁盘名称
     *
     * @return string
     */
    public function disk (): string
    {
        return 'oss';
    }

    /**
     * 保存文件
     * @param UploadedFile $file
     * @param FilePathResult $path
     * @return string
     * @throws FileException
     */
    public function putFileAs (UploadedFile $file, FilePathResult $path): string
    {

        // putFileAs 返回保存后的相对路径（key）
        $result = Storage::disk($this->disk())->putFileAs(
            $path->directory, // 例如：uploads/20260108
            $file,
            $path->filename   // 例如：xxxx.jpg
        );


        if (!$result) {
            throw new FileException('file save fail');
        }

        return $result;
    }
}
