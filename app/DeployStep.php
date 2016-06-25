<?php

namespace REBELinBLUE\Deployer;

use Illuminate\Database\Eloquent\Model;
use REBELinBLUE\Deployer\Presenters\DeployStepPresenter;
use Robbo\Presenter\PresentableInterface;

/**
 * The deployment step model.
 *
 * @property integer $id
 * @property integer $deployment_id
 * @property integer $stage
 * @property integer $command_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read ServerLog[] $servers
 * @property-read Command $command
 */
class DeployStep extends Model implements PresentableInterface
{
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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['stage', 'deployment_id', 'command_id'];

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
     * @return DeployStepPresenter
     */
    public function getPresenter()
    {
        return new DeployStepPresenter($this);
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
