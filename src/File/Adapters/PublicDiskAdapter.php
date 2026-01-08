<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  PublicDiskAdapter.php
// +----------------------------------------------------------------------
// | Year:      2026/1/9/一月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\File\Adapters;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Valencio\LaravelKit\File\FilePathResult;

/**
 * 获取存储的磁盘名称。
 *
 * 该方法用于返回当前适配器关联的磁盘名称，通常用于标识或配置中指定特定的存储位置。
 *
 * @return string 返回表示磁盘名称的字符串。
 */
class PublicDiskAdapter implements StorageAdapterInterface
{

    /**
     * 获取存储的磁盘名称
     *
     * @return string
     */
    public function disk (): string
    {
        return 'public';
    }

    /**
     * 上传文件
     *
     * @param UploadedFile $file
     * @param FilePathResult $path
     * @return string
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
            throw new RuntimeException('file save fail');
        }

        return $result;
    }
}
