<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  BaseRepositoryInterface.php
// +----------------------------------------------------------------------
// | Year:      2025/8/14/八月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\Repository\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 *
 */
interface BaseRepositoryInterface
{
    /**
     * Get a query builder instance with cross-cutting concerns applied.
     *
     * @return Builder
     */
    public function query (): Builder;

    /**
     * Find a record by ID.
     *
     * @param int $id
     * @return Model|null
     */
    public function find (int $id): ?Model;

    /**
     * Find a record by ID or throw an exception.
     *
     * @param int $id
     * @return Model
     * @throws ModelNotFoundException
     */
    public function findOrFail (int $id): Model;


    /**
     * Find the first record matching criteria.
     *
     * @param array $criteria
     * @return Model|null
     */
    public function first (array $criteria): ?Model;

    /**
     * Find the first record matching criteria or throw an exception.
     *
     * @param array $criteria
     * @return Model
     * @throws ModelNotFoundException
     */
    public function firstOrFail (array $criteria): Model;

    /**
     * Create a new record.
     *
     * @param array $data
     * @return Model
     */
    public function create (array $data): Model;


    /**
     * @param array $values
     * @return bool
     */
    public function insert (array $values): bool;

    /**
     * Update a record by ID.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update (int $id, array $data): bool;


    /**
     * Update a record by Model.
     *
     * @param Model $model
     * @param array $data
     * @return bool
     */
    public function updateModel (Model $model, array $data): bool;

    /**
     * Delete a record by Model.
     *
     * @param Model $model
     * @return bool
     */
    public function delete (Model $model): bool;

    /**
     * Destroy the models for the given IDs.
     *
     * @param int|array $ids
     * @return int
     */
    public function destroy (int|array $ids): int;
}
