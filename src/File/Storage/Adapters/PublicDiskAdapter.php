<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  PublicDiskAdapter.php
// +----------------------------------------------------------------------
// | Year:      2026/1/17/一月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\File\Storage\Adapters;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Valencio\LaravelKit\File\Core\Contracts\StorageAdapterInterface;
use Valencio\LaravelKit\File\Core\Results\FilePathResult;
use Valencio\LaravelKit\File\Exceptions\FileException;

/**
 * 公共磁盘存储适配器
 */
class PublicDiskAdapter implements StorageAdapterInterface
{
    /**
     * @return string
     */
    public function disk (): string
    {
        return 'public';
    }

    /**
     * @param UploadedFile $file
     * @param FilePathResult $path
     * @return string
     * @throws FileException
     */
    public function putFileAs (UploadedFile $file, FilePathResult $path): string
    {
        $result = Storage::disk($this->disk())->putFileAs(
            $path->directory,
            $file,
            $path->filename
        );

        if (!$result) {
            throw new FileException('file save fail');
        }

        return $result;
    }

    /**
     * @param string $path
     * @param string|null $filename
     * @return StreamedResponse
     * @throws FileException
     */
    public function download (string $path, ?string $filename = null): StreamedResponse
    {
        if (!$this->exists($path)) {
            throw new FileException("File not found: {$path}");
        }


        return Storage::disk($this->disk())->download($path, $filename);
    }

    /**
     * @param string $path
     * @return bool
     */
    public function exists (string $path): bool
    {
        return Storage::disk($this->disk())->exists($path);
    }
}
