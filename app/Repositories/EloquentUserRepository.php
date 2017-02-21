<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\Repositories\Contracts\UserRepositoryInterface;
use REBELinBLUE\Deployer\User;

/**
 * The user repository.
 */
class EloquentUserRepository extends EloquentRepository implements UserRepositoryInterface
{
    /**
     * EloquentUserRepository constructor.
     *
     * @param User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Creates a new instance of the model.
     *
     * @param array $fields
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $fields)
    {
        $fields['password'] = bcrypt($fields['password']);

        return $this->model->create($fields);
    }

    /**
     * Updates an instance by it's ID.
     *
     * @param array $fields
     * @param int   $model_id
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function updateById(array $fields, $model_id)
    {
        $user = $this->getById($model_id);

        if (array_key_exists('password', $fields)) {
            if (empty($fields['password'])) {
                unset($fields['password']);
            } else {
                $fields['password'] = bcrypt($fields['password']);
            }
        }

        $user->update($fields);

        return $user;
    }

    /**
     * @param string $token
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function findByEmailToken($token)
    {
        return $this->model->where('email_token', $token)->first();
    }

    /**
     * @param string $email
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }
}
