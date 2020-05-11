<?php

namespace REBELinBLUE\Deployer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use McCool\LaravelAutoPresenter\HasPresenter;
use REBELinBLUE\Deployer\View\Presenters\RuntimeInterface;
use REBELinBLUE\Deployer\View\Presenters\ServerLogPresenter;

/**
 * Server log model.
 */
class ServerLog extends Model implements HasPresenter, RuntimeInterface
{
    public const COMPLETED = 0;
    public const PENDING   = 1;
    public const RUNNING   = 2;
    public const FAILED    = 3;
    public const CANCELLED = 4;

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
     * The fields which should be treated as Carbon instances.
     *
     * @var array
     */
    protected $dates = ['started_at', 'finished_at'];

    /**
     * Belongs to association.
     *
     * @return BelongsTo
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
     * @return string
     */
    public function getPresenterClass(): string
    {
        return ServerLogPresenter::class;
    }
}
