<?php

namespace REBELinBLUE\Deployer;

use Illuminate\Database\Eloquent\Model;
use REBELinBLUE\Deployer\Presenters\DeployStepPresenter;
use Robbo\Presenter\PresentableInterface;

/**
 * The deployment step model.
 */
class DeployStep extends Model implements PresentableInterface
{
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'stage'    => 'integer',
        'optional' => 'boolean',
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
        return $this->hasMany('REBELinBLUE\Deployer\ServerLog');
    }

    /**
     * Belong to relationship.
     *
     * @return Command
     */
    public function command()
    {
        return $this->belongsTo('REBELinBLUE\Deployer\Command')
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
}
