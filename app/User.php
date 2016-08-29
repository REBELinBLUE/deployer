<?php

namespace REBELinBLUE\Deployer;

use Illuminate\Database\Eloquent\SoftDeletes;
use REBELinBLUE\Deployer\Presenters\UserPresenter;
use REBELinBLUE\Deployer\Traits\BroadcastChanges;
use Robbo\Presenter\PresentableInterface;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use REBELinBLUE\Deployer\Notifications\ResetPassword as ResetPasswordNotification;

/**
 * User model.
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @property string $email_token
 * @property string $avatar
 * @property string $language
 * @property string $skin
 * @property string $google2fa_secret
 * @property string $scheme
 * @property-read mixed $has_two_factor_authentication
 */
class User extends Authenticatable implements PresentableInterface
{
    use SoftDeletes, BroadcastChanges, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'skin', 'language', 'scheme'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['deleted_at', 'updated_at', 'password', 'remember_token', 'google2fa_secret'];

    /**
     * Additional attributes to include in the JSON representation.
     *
     * @var array
     */
    protected $appends = ['has_two_factor_authentication'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    /**
     * Generate a change email token.
     *
     * @return string
     */
    public function requestEmailToken()
    {
        $this->email_token = str_random(40);
        $this->save();

        return $this->email_token;
    }

    /**
     * Gets the view presenter.
     *
     * @return UserPresenter
     */
    public function getPresenter()
    {
        return new UserPresenter($this);
    }

    /**
     * A hack to allow avatar_url to be called on the result of Auth::user().
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        if ($key === 'avatar_url') {
            return $this->getPresenter()->avatar_url;
        }

        return parent::__get($key);
    }

    /**
     * Determines whether the user has Google 2FA enabled.
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getHasTwoFactorAuthenticationAttribute()
    {
        return !empty($this->google2fa_secret);
    }

    /**
     * Send the password reset notification.
     *
     * @param string $token
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
