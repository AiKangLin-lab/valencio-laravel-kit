<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  FileException.php
// +----------------------------------------------------------------------
// | Year:      2026/1/9/一月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\File\Exceptions;

use Exception;
use Throwable;


/**
 * 文件异常类，用于处理文件相关的错误。
 */
class FileException extends Exception
{
    /**
     * 构造上传异常
     *
     * @param string $message 异常信息
     * @param int $code 异常代码
     * @param Throwable|null $previous 前置异常
     */
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
