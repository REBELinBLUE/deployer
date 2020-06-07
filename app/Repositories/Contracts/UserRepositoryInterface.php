<?php

namespace REBELinBLUE\Deployer\Repositories\Contracts;

use REBELinBLUE\Deployer\User;

interface UserRepositoryInterface
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll();

    /**
     * @param array $fields
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $fields);

    /**
     * @param array $fields
     * @param int   $model_id
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function updateById(array $fields, int $model_id);

    /**
     * @param int $model_id
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return bool
     */
    public function deleteById(int $model_id);

    /**
     * @param string $token
     *
     * @return User|null
     */
    public function findByEmailToken(string $token): ?User;

    /**
     * @param string $email
     *
     * @return User|null
     */
    public function findByEmail(string $email): ?User;
}
