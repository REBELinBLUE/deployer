<?php

namespace App\Repositories;

use App\Events\UserWasCreated;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\EloquentRepository;
use App\User;

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
}
