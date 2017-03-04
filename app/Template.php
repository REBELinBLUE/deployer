<?php

namespace REBELinBLUE\Deployer;

use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;
use REBELinBLUE\Deployer\Traits\ProjectRelations;
use REBELinBLUE\Deployer\View\Presenters\CommandPresenter;

/**
 * Model for templates.
 */
class Template extends Model implements HasPresenter
{
    use ProjectRelations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Fields to show in the JSON presentation.
     *
     * @var array
     */
    protected $visible = ['id', 'name', 'command_count', 'file_count', 'config_count', 'variable_count'];

    /**
     * Additional attributes to include in the JSON representation.
     *
     * @var array
     */
    protected $appends = ['command_count', 'file_count', 'config_count', 'variable_count'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    /**
     * Define a accessor for the count of projects.
     *
     * @return int
     */
    public function getCommandCountAttribute()
    {
        return $this->commands
                    ->count();
    }

    /**
     * Define a accessor for the count of persistent files.
     *
     * @return int
     */
    public function getFileCountAttribute()
    {
        return $this->sharedFiles
                    ->count();
    }

    /**
     * Define a accessor for the count of config files.
     *
     * @return int
     */
    public function getConfigCountAttribute()
    {
        return $this->configFiles
                    ->count();
    }

    /**
     * Define a accessor for the count of env variables.
     *
     * @return int
     */
    public function getVariableCountAttribute()
    {
        return $this->variables
                    ->count();
    }

    /**
     * Gets the view presenter.
     *
     * @return string
     */
    public function getPresenterClass()
    {
        return CommandPresenter::class;
    }
}
