<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  upload.php
// +----------------------------------------------------------------------
// | Year:      2025/8/6/八月
// +----------------------------------------------------------------------
declare (strict_types=1);


use Valencio\LaravelKit\Upload\Enums\Engine;

return [
    // 默认上传驱动
    'default'    => Engine::Local,

    // 文件命名策略（如 random, md5, sha1）
    'naming'     => 'md5',

    // 各个驱动的配置
    'drivers'    => [
        Engine::Local->value => [
            'disk' => 'public',
            'prefix' => '/storage/uploads', // 返回路径的前缀
        ],
    ],

    'validation' => [
        // 是否启用内置验证，可一键关闭
        'enabled'  => true,

        // 定义不同场景的验证规则集
        'rulesets' => [
            // 默认规则集，如果不指定场景，则使用此规则
            'default' => [
                'file', // 必须是一个有效的文件
                'mimes:jpeg,jpg,gif,png,mp4,mp3,xls,xlsx,doc,docx,md,pdf,json,txt,zip',
                'extensions:jpeg,jpg,gif,png,mp4,mp3,xls,xlsx,doc,docx,md,pdf,json,txt,zip',
                'max:2048', // 最大体积 2MB
            ]
        ],
    ],
];
