<?php

namespace REBELinBLUE\Deployer;

use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;
use REBELinBLUE\Deployer\View\Presenters\DeployStepPresenter;

/**
 * The deployment step model.
 */
class DeployStep extends Model implements HasPresenter
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['stage', 'deployment_id', 'command_id'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'            => 'integer',
        'command_id'    => 'integer',
        'deployment_id' => 'integer',
        'stage'         => 'integer',
        'optional'      => 'boolean',
    ];

    /**
     * Has many relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function servers()
    {
        return $this->hasMany(ServerLog::class);
    }

    /**
     * Belong to relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function command()
    {
        return $this->belongsTo(Command::class)
                    ->withTrashed();
    }

    /**
     * Gets the view presenter.
     *
     * @return string
     */
    public function getPresenterClass()
    {
        return DeployStepPresenter::class;
    }

    /**
     * Determines if the step is a BEFORE or AFTER step.
     *
     * @return bool
     */
    public function isCustom()
    {
        return (!in_array($this->stage, [
            Command::DO_CLONE,
            Command::DO_INSTALL,
            Command::DO_ACTIVATE,
            Command::DO_PURGE,
        ], true));
    }
}
