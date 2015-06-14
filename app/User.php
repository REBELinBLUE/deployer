<?php

namespace App;

use App\Events\ModelChanged;
use App\Events\ModelCreated;
use App\Events\ModelTrashed;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * User model.
 */
class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['deleted_at', 'updated_at', 'password', 'remember_token'];

    /**
     * Override the boot method to bind model event listeners.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::created(function (User $model) {
            event(new ModelCreated($model, 'user'));
        });

        static::updated(function (User $model) {
            event(new ModelChanged($model, 'user'));
        });

        static::deleted(function (User $model) {
            event(new ModelTrashed($model, 'user'));
        });
    }
}
