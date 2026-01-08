<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  FilePathResult.php
// +----------------------------------------------------------------------
// | Year:      2026/1/8/一月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\File;


/**
 *
 */
readonly class FilePathResult
{
    /**
     * @param string $directory
     * @param string $filename
     * @param string $extension
     * @param string $namingStrategy
     */
    public function __construct(
        public string $directory,
        public string $filename,
        public string $extension,
        public string $namingStrategy,
    ) {}

    public function relativePath(): string
    {
        return trim($this->directory . '/' . $this->filename, '/');
    }
}
