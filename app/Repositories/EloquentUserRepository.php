<?php

namespace REBELinBLUE\Deployer\Repositories;

use REBELinBLUE\Deployer\Events\UserWasCreated;
use REBELinBLUE\Deployer\Repositories\Contracts\UserRepositoryInterface;
use REBELinBLUE\Deployer\Repositories\EloquentRepository;
use REBELinBLUE\Deployer\User;

/**
 * The user repository.
 */
class EloquentUserRepository extends EloquentRepository implements UserRepositoryInterface
{
    /**
     * Class constructor.
     *
     * @param  User                   $model
     * @return EloquentUserRepository
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Creates a new instance of the user.
     *
     * @param  array $fields
     * @return Model
     */
    public function create(array $fields)
    {
        $password           = $fields['password'];
        $fields['password'] = bcrypt($fields['password']);

        $user = $this->model->create($fields);

        event(new UserWasCreated($user, $password));

        return $user;
    }

    /**
     * Update an instance of the user.
     *
     * @param  array $fields
     * @return Model
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
     * Find user by the email token.
     * @param  string $token
     * @return User
     */
    public function findByEmailToken($token)
    {
        return $this->model->where('email_token', $token)->first();
    }
}
