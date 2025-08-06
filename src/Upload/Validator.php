<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  Validator.php
// +----------------------------------------------------------------------
// | Year:      2025/8/6/八月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\Upload;

use Illuminate\Support\Facades\Validator as LaravelValidator;
use Illuminate\Http\UploadedFile;

/**
 * 上传文件验证器
 *
 * 封装 Laravel 内置 Validator，专用于上传文件的规则校验。
 */
class Validator
{
    /**
     * 执行上传文件的验证
     *
     * @param UploadedFile $file 待验证的文件对象
     * @param array $rules 验证规则
     * @return void
     * @throws \Illuminate\Validation\ValidationException 验证失败时抛出
     */
    public function execute(UploadedFile $file, array $rules): void
    {
        // 我们不重复造轮子，而是直接使用 Laravel 强大的内置 Validator
        $validator = LaravelValidator::make(
            ['upload_file' => $file],
            ['upload_file' => $rules],
            [],
            [
                'upload_file' => __('kit::upload.file')
            ]
        );

        // validate() 会在验证失败时抛出 ValidationException
        // 这和在 Controller 中调用 $request->validate() 的行为完全一致
        $validator->validate();
    }
}
