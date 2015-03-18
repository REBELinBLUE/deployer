<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class DeployLog extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'deploy_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['deployment_id', 'stage', 'command_id', 'run_order', 'output'];
}
