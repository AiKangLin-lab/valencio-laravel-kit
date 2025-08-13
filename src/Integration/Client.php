<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  Client.php
// +----------------------------------------------------------------------
// | Year:      2025/8/13/八月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\Integration;

use Exception;
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\RequestInterface;

/**
 * 集成服务HTTP客户端
 *
 * 提供统一的HTTP请求处理，支持日志记录和错误处理
 *
 * @package Valencio\LaravelKit\Integration
 * @author ValencioKang <ailin1219@foxmail.com>
 * @since 2025-08-13
 */
class Client
{
    use Properties;
    use Tools;

    /**
     * 验证日志通道配置
     *
     * @throws IntegrationException 当日志通道配置无效时抛出异常
     * @return void
     */
    private function validateLogChannels(): void
    {
        $availableChannels = array_keys(config('logging.channels', []));

        if (empty($this->logChannel) || empty($this->infoLogChannel)) {
            throw new IntegrationException('日志通道配置不能为空');
        }

        if (!in_array($this->logChannel, $availableChannels)) {
            throw new IntegrationException("错误日志通道 '{$this->logChannel}' 不存在");
        }

        if (!in_array($this->infoLogChannel, $availableChannels)) {
            throw new IntegrationException("信息日志通道 '{$this->infoLogChannel}' 不存在");
        }
    }

    /**
     * 初始化HTTP客户端
     *
     * @return void
     * @throws IntegrationException
     */
    private function initialize(): void
    {
        $this->validateLogChannels();

        $this->pendingRequest = Http::withRequestMiddleware(
            function (RequestInterface $request) {
                $this->request = $request;
                return $request;
            }
        )->connectTimeout($this->connectTimeout)
         ->timeout($this->timeout);
    }

    /**
     * 发送HTTP请求
     *
     * @param string $url 请求URL
     * @param array<string, mixed> $options 请求选项
     * @param string $method HTTP方法 (GET|POST|PUT|DELETE|PATCH)
     * @param bool $handleResponse 是否自动处理响应
     * @return bool 请求是否成功
     * @throws IntegrationException 当请求配置错误时抛出异常
     */
    public function send(
        string $url,
        array $options = [],
        string $method = 'POST',
        bool $handleResponse = true
    ): bool {
        try {
            $this->initialize();

            $response = $this->pendingRequest->send($method, $url, $options);

            if ($handleResponse) {
                return $this->handleResponse($response);
            }

            return $response->successful();

        } catch (IntegrationException $e) {
            // 重新抛出配置相关异常
            throw $e;
        } catch (Exception $e) {
            $this->writeLog($e);
            return false;
        }
    }

    /**
     * 发送GET请求
     *
     * @param string $url 请求URL
     * @param array<string, mixed> $options 请求选项
     * @param bool $handleResponse 是否自动处理响应
     * @return bool
     * @throws IntegrationException
     */
    public function get(string $url, array $options = [], bool $handleResponse = true): bool
    {
        return $this->send($url, $options, 'GET', $handleResponse);
    }

    /**
     * 发送POST请求
     *
     * @param string $url 请求URL
     * @param array<string, mixed> $options 请求选项
     * @param bool $handleResponse 是否自动处理响应
     * @return bool
     * @throws IntegrationException
     */
    public function post(string $url, array $options = [], bool $handleResponse = true): bool
    {
        return $this->send($url, $options, 'POST', $handleResponse);
    }

    /**
     * 发送PUT请求
     *
     * @param string $url 请求URL
     * @param array<string, mixed> $options 请求选项
     * @param bool $handleResponse 是否自动处理响应
     * @return bool
     * @throws IntegrationException
     */
    public function put(string $url, array $options = [], bool $handleResponse = true): bool
    {
        return $this->send($url, $options, 'PUT', $handleResponse);
    }

    /**
     * 发送DELETE请求
     *
     * @param string $url 请求URL
     * @param array<string, mixed> $options 请求选项
     * @param bool $handleResponse 是否自动处理响应
     * @return bool
     * @throws IntegrationException
     */
    public function delete(string $url, array $options = [], bool $handleResponse = true): bool
    {
        return $this->send($url, $options, 'DELETE', $handleResponse);
    }
}
