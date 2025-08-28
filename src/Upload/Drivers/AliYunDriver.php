<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  AliYunDriver.php
// +----------------------------------------------------------------------
// | Year:      2025/8/28/八月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\Upload\Drivers;

use AlibabaCloud\Oss\V2\Client;
use AlibabaCloud\Oss\V2\Config;
use AlibabaCloud\Oss\V2\Credentials\EnvironmentVariableCredentialsProvider;
use AlibabaCloud\Oss\V2\Credentials\StaticCredentialsProvider;
use AlibabaCloud\Oss\V2\Models\PutObjectAclRequest;
use AlibabaCloud\Oss\V2\Models\PutObjectRequest;
use AlibabaCloud\Oss\V2\Utils;
use Illuminate\Http\UploadedFile;
use Valencio\LaravelKit\Upload\Contracts\Uploader;
use Valencio\LaravelKit\Upload\UploadException;

class AliYunDriver implements Uploader
{
    /**
     * 驱动配置
     * @var array
     */
    protected array $config;

    /**
     * 构造函数
     * @param array $config 驱动配置
     */
    public function __construct (array $config)
    {
        $this->config = $config;
    }


    /**
     * 执行上传
     *
     * @param UploadedFile $file
     * @param string|null $path
     * @param string|null $filename
     * @return string|false
     * @throws UploadException
     */
    public function store (UploadedFile $file, ?string $path = null, ?string $filename = null): string|false
    {

        try {


            $key = $path . '/' . $filename;

            $client = $this->getClient();

            // 要上传的数据内容
            $data = $file->getContent();

            // 创建PutObjectRequest对象，用于上传对象
            $request = new PutObjectRequest(
                bucket: $this->config['bucket'],
                key: $key,
            );

            $request->body = Utils::streamFor($data); // 设置请求体为数据流


            // 执行上传操作
            $result = $client->putObject($request);

            // 打印上传结果
            if ($result->statusCode == 200) {
                return $key;
            }
            return  false;
        } catch (\Throwable $e) {
            throw new UploadException(
                __('kit::upload.local_store_failed', ['msg' => $e->getMessage()]),
                0,
                $e
            );
        }

    }

    /**
     * @return Client
     */
    private function getClient (): Client
    {

        $credentialsProvider = new StaticCredentialsProvider(
            $this->config['access_key_id'],
            $this->config['access_key_secret'],
        );

        # 加载SDK的默认配置，并设置凭证提供者
        $cfg = Config::loadDefault();

        $cfg->setCredentialsProvider(credentialsProvider: $credentialsProvider);


        $cfg->setRegion(region: $this->config['region']);

        // 创建OSS客户端实例
        return new Client($cfg);
    }

}
