<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  Tools.php
// +----------------------------------------------------------------------
// | Year:      2025/8/13/八月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\Integration;

use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * 集成服务工具特征
 * 
 * 提供响应处理和日志记录等工具方法
 * 
 * @package Valencio\LaravelKit\Integration
 * @author ValencioKang <ailin1219@foxmail.com>
 * @since 2025-08-13
 */
trait Tools
{
    /**
     * 处理HTTP响应结果
     *
     * @param Response $response HTTP响应实例
     * @return bool 处理是否成功
     */
    public function handleResponse(Response $response): bool
    {
        $this->response = $response;
        
        // 检查HTTP状态码是否失败
        if ($response->failed()) {
            $this->writeLog(
                otherDescription: 'HTTP请求失败，状态码: ' . $response->status()
            );
            return false;
        }

        $this->isSuccess = $response->successful();

        // 解析响应数据
        $this->result = $response->json() ?? [];
        $this->resultCollection = $response->collect();

        // 检查业务逻辑错误码
        if ($this->hasBusinessError()) {
            $this->writeLog(
                otherDescription: '业务逻辑错误，错误码: ' . ($this->resultCollection[$this->errorCodeField] ?? 'unknown')
            );
            $this->isSuccess = false;
            return false;
        }

        // 记录成功日志
        if ($this->enableLog) {
            $this->logChannel = $this->infoLogChannel;
            $this->writeLog(logType: 'info');
        }

        return true;
    }

    /**
     * 检查是否存在业务逻辑错误
     *
     * @return bool
     */
    private function hasBusinessError(): bool
    {
        return $this->resultCollection->has($this->errorCodeField) 
            && $this->resultCollection[$this->errorCodeField] != $this->successCode;
    }

    /**
     * 写入日志记录
     *
     * @param Throwable|Exception|null $exception 异常实例
     * @param string $logType 日志类型 (error|info|warning|debug)
     * @param string|null $otherDescription 额外描述信息
     * @return void
     */
    public function writeLog(
        Throwable|Exception|null $exception = null,
        string $logType = 'error',
        ?string $otherDescription = null,
    ): void {
        $logContent = $this->buildLogContent($exception, $otherDescription);
        
        Log::channel($this->logChannel)->{$logType}($logContent);
    }

    /**
     * 构建日志内容
     *
     * @param Throwable|Exception|null $exception
     * @param string|null $otherDescription
     * @return string
     */
    private function buildLogContent(
        Throwable|Exception|null $exception = null,
        ?string $otherDescription = null
    ): string {
        $content = [];
        $content[] = '--------------------------------------Start--------------------------------------';

        // 添加描述信息
        if ($this->description) {
            $content[] = 'Description: ' . $this->description;
        }
        
        if ($otherDescription) {
            $content[] = 'OtherDescription: ' . $otherDescription;
        }

        // 添加请求信息
        if ($this->request) {
            $content[] = 'URL: ' . $this->request->getUri();
            $content[] = 'Method: ' . $this->request->getMethod();
            $content[] = 'Headers: ' . json_encode($this->request->getHeaders(), JSON_UNESCAPED_UNICODE);
            $content[] = 'Body: ' . (string) $this->request->getBody();
        }

        // 添加响应结果
        if (!empty($this->result)) {
            $content[] = 'Result: ' . json_encode($this->result, JSON_UNESCAPED_UNICODE);
        }

        // 添加异常信息
        if ($exception) {
            $content[] = 'ExceptionMessage: ' . $exception->getMessage();
            $content[] = 'ExceptionFile: ' . $exception->getFile();
            $content[] = 'ExceptionLine: ' . $exception->getLine();
            $content[] = 'ExceptionClass: ' . get_class($exception);
            $content[] = 'ExceptionCode: ' . $exception->getCode();
        }

        $content[] = '--------------------------------------End--------------------------------------';

        return implode("\n", $content);
    }
}
