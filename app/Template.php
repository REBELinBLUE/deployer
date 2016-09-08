<?php

namespace REBELinBLUE\Deployer;

use Illuminate\Database\Eloquent\Model;
use REBELinBLUE\Deployer\Presenters\CommandPresenter;
use REBELinBLUE\Deployer\Traits\ProjectRelations;
use Robbo\Presenter\PresentableInterface;

/**
 * Model for templates.
 *
 * @property integer $id
 * @property string $name
 * @property string $repository
 * @property string $hash
 * @property string $branch
 * @property string $private_key
 * @property string $public_key
 * @property integer $group_id
 * @property integer $builds_to_keep
 * @property string $url
 * @property string $build_url
 * @property string $status
 * @property string $last_run
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @property boolean $is_template
 * @property string $last_mirrored
 * @property boolean $allow_other_branch
 * @property boolean $include_dev
 * @property-read mixed $command_count
 * @property-read mixed $file_count
 * @property-read mixed $config_count
 * @property-read mixed $variable_count
 * @property-read Command[] $commands
 * @property-read SharedFile[] $sharedFiles
 * @property-read ConfigFile[] $configFiles
 * @property-read Variable[] $variables
 */
class Template extends Model implements PresentableInterface
{
    use ProjectRelations;

    /**
     * Fields to show in the JSON presentation.
     *
     * @var array
     */
    protected $visible = ['id', 'name', 'command_count', 'file_count', 'config_count', 'variable_count'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

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
     * Query scope to only show templates.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTemplates($query)
    {
        return $query->where('is_template', '=', true);
    }

    /**
     * Define a accessor for the count of projects.
     *
     * @return int
     */
    public function getCommandCountAttribute()
    {
        return $this->commands()
                    ->count();
    }

    /**
     * Define a accessor for the count of persistent files.
     *
     * @return int
     */
    public function getFileCountAttribute()
    {
        return $this->sharedFiles()
                    ->count();
    }

    /**
     * Define a accessor for the count of config files.
     *
     * @return int
     */
    public function getConfigCountAttribute()
    {
        return $this->configFiles()
                    ->count();
    }

    /**
     * Define a accessor for the count of env variables.
     *
     * @return int
     */
    public function getVariableCountAttribute()
    {
        return $this->variables()
                    ->count();
    }

    /**
     * Gets the view presenter.
     *
     * @return CommandPresenter
     */
    public function getPresenter()
    {
        return new CommandPresenter($this);
    }
}
