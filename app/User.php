<?php

namespace REBELinBLUE\Deployer;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use McCool\LaravelAutoPresenter\HasPresenter;
use REBELinBLUE\Deployer\Notifications\System\ResetPassword;
use REBELinBLUE\Deployer\Traits\BroadcastChanges;
use REBELinBLUE\Deployer\View\Presenters\UserPresenter;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * User model.
 */
class User extends Authenticatable implements HasPresenter, JWTSubject
{
    use SoftDeletes, BroadcastChanges, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'skin', 'language', 'scheme', 'is_admin'];

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
        'id'        => 'integer',
        'is_admin'  => 'boolean',
    ];

    /**
     * Generate a change email token.
     *
     * @return string
     */
    public function requestEmailToken(): string
    {
        $this->email_token = token(40);
        $this->save();

        return $this->email_token;
    }

    /**
     * Gets the view presenter.
     *
     * @return string
     */
    public function getPresenterClass(): string
    {
        return UserPresenter::class;
    }

    /**
     * Determines whether the user has Google 2FA enabled.
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getHasTwoFactorAuthenticationAttribute(): bool
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
        // FIXME: Clean this up?
        $this->notify(new ResetPassword($token, app('translator')));
    }

    /**
     * Has many relationship for projects.
     *
     * @return BelongsToMany
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }

    /**
     * Is this user a super administrator.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [
            'data' => [
                'userId' => $this->id,
            ],
        ];
    }
}
