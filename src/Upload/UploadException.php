<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  UploadException.php
// +----------------------------------------------------------------------
// | Year:      2025/8/6/八月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\Upload;

use Exception;
use Throwable;

/**
 * 上传相关的统一异常类
 *
 * 所有上传相关的自定义异常都应继承本类，便于统一 catch 和处理。
 *
 * @package Valencio\LaravelKit\Upload
 */
class UploadException extends Exception
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
