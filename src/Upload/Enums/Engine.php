<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  Engine.php
// +----------------------------------------------------------------------
// | Year:      2025/8/6/八月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\Upload\Enums;

/**
 *
 */
enum Engine: string
{
    case Local = 'local';
    case Aliyun = 'aliyun';
    case Tencent = 'tencent';
}
