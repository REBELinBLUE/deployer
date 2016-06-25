<?php

namespace REBELinBLUE\Deployer;

use Illuminate\Database\Eloquent\Model;
use REBELinBLUE\Deployer\Presenters\CommandPresenter;
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
 * @property-read ProjectFile[] $projectFiles
 * @property-read Variable[] $variables
 */
class Template extends Model implements PresentableInterface
{
    /**
     * All templates belong in group 1.
     */
    const GROUP_ID = 1;

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
    protected $fillable = ['name', 'is_template', 'group_id'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'projects';

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
        'id'          => 'integer',
        'group_id'    => 'integer',
        'is_template' => 'boolean',
    ];

    /**
     * Override the boot method to bind model event listeners.
     */
    public static function boot()
    {
        parent::boot();

        // Set the required fields to empty values
        static::creating(function (Template $model) {
            $model->group_id = static::GROUP_ID;
            $model->is_template = true;
            $model->repository = '';
            $model->hash = '';
            $model->private_key = '';
            $model->public_key = '';

            return true;
        });
    }

    /**
     * Query scope to only show templates.
     *
     * @param  object $query
     * @return object
     * @todo figure out the docblock here
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
        return $this->projectFiles()
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
     * Has many relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function commands()
    {
        return $this->hasMany(Command::class, 'project_id');
    }

    /**
     * Has many relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sharedFiles()
    {
        return $this->hasMany(SharedFile::class, 'project_id');
    }

    /**
     * Has many relationship to project file.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projectFiles()
    {
        return $this->hasMany(ProjectFile::class, 'project_id');
    }

    /**
     * Has many relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function variables()
    {
        return $this->hasMany(Variable::class, 'project_id');
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
