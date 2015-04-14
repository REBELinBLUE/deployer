<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Server model
 */
class Server extends Model
{
    use SoftDeletes;

    const SUCCESSFUL = 0;
    const UNTESTED   = 1;
    const FAILED     = 2;
    const TESTING    = 3;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['project_id', 'created_at', 'updated_at', 'deleted_at', 'pivot'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'user', 'ip_address', 'project_id', 'path', 'status', 'deploy_code'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'status'      => 'integer',
        'deploy_code' => 'boolean'
    ];

    /**
     * Belongs to relationship
     *
     * @return Project
     */
    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    /**
     * Define a mutator for the user, if it has changed or has
     * not previously been set also set the status to untested
     * 
     * @param string $value
     * @return void
     */
    public function setUserAttribute($value)
    {
        if (!array_key_exists('user', $this->attributes) || $value !== $this->attributes['user']) {
            $this->attributes['status'] = self::UNTESTED;
        }

        $this->attributes['user'] = $value;
    }

    /**
     * Define a mutator for the IP Address, if it has changed or
     * has not previously been set also set the status to untested
     * 
     * @param string $value
     * @return void
     */
    public function setIpAddressAttribute($value)
    {
        if (!array_key_exists('ip_address', $this->attributes) || $value !== $this->attributes['ip_address']) {
            $this->attributes['status'] = self::UNTESTED;
        }

        $this->attributes['ip_address'] = $value;
    }
}
