<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  Core.php
// +----------------------------------------------------------------------
// | Year:      2025/8/7/八月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\Curd;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * CRUD操作属性特征
 *
 * 定义CRUD操作所需的所有属性和配置项，包括模型、查询构建器、排序、搜索等
 *
 * @package Valencio\LaravelKit\Curd
 * @author ValencioKang <ailin1219@foxmail.com>
 * @since 2025-08-07
 */
trait Properties
{
    /**
     * Eloquent模型实例
     *
     * @var Model
     */
    protected Model $model;

    /**
     * 当前查询构建器实例
     *
     * @var Builder
     */
    protected Builder $currentBuilder;

    /**
     * 排序字段名称
     *
     * @var string
     */
    protected string $sortField = 'id';

    /**
     * 排序方向 (asc|desc)
     *
     * @var string
     */
    protected string $sortOrder = 'desc';

    /**
     * 系统自定义排序字段配置
     *
     * 格式: ['字段名' => '排序方向']
     *
     * @var array<string, string>
     */
    protected array $systemSortFields = [];

    /**
     * 是否为更新操作标识
     *
     * @var bool
     */
    protected bool $isUpdate = false;

    /**
     * 待操作的数据数组
     *
     * @var array<string, mixed>
     */
    protected array $data = [];

    /**
     * 支持模糊查询的字段列表
     *
     * 这些字段将使用LIKE查询进行模糊匹配
     *
     * @var array<string>
     */
    protected array $searchLikeFields = [];

    /**
     * 支持精确查询的字段列表
     *
     * 这些字段将使用等号进行精确匹配
     *
     * @var array<string>
     */
    protected array $searchEqualFields = [];

    /**
     * 需要预加载的关联模型
     *
     * @var array<string>
     */
    protected array $with = [];

    /**
     * 查询字段列表
     *
     * 分页指定查询时要返回的字段，为空时返回所有字段
     *
     * @var array<string>
     */
    protected array $columns = [];

    /**
     * 列表查询时返回的字段列表
     *
     * @var array<string>
     */
    protected array $listColumns = [];

    /**
     * 请求参数数组
     *
     * 存储来自HTTP请求的所有参数
     *
     * @var array<string, mixed>
     */
    protected array $requestParameters = [];

    /**
     * 可更新的字段列表
     *
     * 这些字段将允许更新
     *
     * @var array<string>
     */
    protected array $allowUpdateFields = [];
}
