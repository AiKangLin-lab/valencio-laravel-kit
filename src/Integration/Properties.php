<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  Properties.php
// +----------------------------------------------------------------------
// | Year:      2025/8/13/八月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\Integration;

use GuzzleHttp\Psr7\Request;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Psr\Http\Message\RequestInterface;

/**
 * 集成服务属性特征
 *
 * 定义集成服务所需的所有属性和配置项
 *
 * @package Valencio\LaravelKit\Integration
 * @author ValencioKang <ailin1219@foxmail.com>
 * @since 2025-08-13
 */
trait Properties
{
    /**
     * HTTP客户端请求实例
     *
     * @var PendingRequest
     */
    protected PendingRequest $pendingRequest;


    /**
     * 基础URL
     *
     * @var string
     */
    protected string $baseUrl = '';

    /**
     * HTTP响应实例
     *
     * @var Response|null
     */
    public ?Response $response = null;

    /**
     * 当前请求实例
     *
     * @var Request|RequestInterface|null
     */
    public Request|RequestInterface|null $request = null;

    /**
     * 连接超时时间（秒）
     *
     * @var int
     */
    protected int $connectTimeout = 10;

    /**
     * 请求超时时间（秒）
     *
     * @var int
     */
    protected int $timeout = 10;

    /**
     * 操作描述信息
     *
     * @var string
     */
    protected string $description = '';

    /**
     * 请求是否成功
     *
     * @var bool
     */
    public bool $isSuccess = false;

    /**
     * 错误码字段名
     *
     * @var string
     */
    protected string $errorCodeField = 'code';

    /**
     * 成功状态码
     *
     * @var int
     */
    protected int $successCode = 200;

    /**
     * 是否启用请求日志记录
     *
     * 当此值为true时，无论请求是否成功都会记录日志
     *
     * @var bool
     */
    public bool $enableLog = false;

    /**
     * 错误日志通道名称
     *
     * @var string
     */
    public string $logChannel = 'integrations_error';

    /**
     * 信息日志通道名称
     *
     * @var string
     */
    public string $infoLogChannel = 'integrations_info';

    /**
     * 响应结果数组
     *
     * @var array<string, mixed>
     */
    public array $result = [];

    /**
     * 响应结果集合
     *
     * @var Collection|null
     */
    public ?Collection $resultCollection = null;
}
