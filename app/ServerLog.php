<?php

namespace REBELinBLUE\Deployer;

use Illuminate\Database\Eloquent\Model;
use REBELinBLUE\Deployer\Contracts\RuntimeInterface;
use REBELinBLUE\Deployer\Events\ServerLogChanged;
use REBELinBLUE\Deployer\Events\ServerOutputChanged;
use REBELinBLUE\Deployer\Presenters\ServerLogPresenter;
use Robbo\Presenter\PresentableInterface;

/**
 * Server log model.
 */
class ServerLog extends Model implements PresentableInterface, RuntimeInterface
{
    const COMPLETED = 0;
    const PENDING   = 1;
    const RUNNING   = 2;
    const FAILED    = 3;
    const CANCELLED = 4;

    /**
     * The fields which should be tried as Carbon instances.
     *
     * @var array
     */
    protected $dates = ['started_at', 'finished_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['server_id', 'deploy_step_id'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'             => 'integer',
        'server_id'      => 'integer',
        'deploy_step_id' => 'integer',
        'status'         => 'integer',
    ];

    /**
     * Override the boot method to bind model event listeners.
     * @fires ServerLogChanged
     * @fires ServerOutputChanged
     */
    public static function boot()
    {
        parent::boot();

        static::updated(function (ServerLog $model) {
            $outputChanged = $model->isDirty('output');

            event(new ServerLogChanged($model));

            if ($outputChanged) {
                event(new ServerOutputChanged($model));
            }
        });
    }

    /**
     * Belongs to association.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    /**
     * Calculates how long the commands were running on the server for.
     *
     * @return false|int Returns false if the command has not yet finished or the runtime in seconds
     */
    public function runtime()
    {
        if (!$this->finished_at) {
            return false;
        }

        return $this->started_at->diffInSeconds($this->finished_at);
    }

    /**
     * Gets the view presenter.
     *
     * @return ServerLogPresenter
     */
    public function getPresenter()
    {
        return new ServerLogPresenter($this);
    }
}
