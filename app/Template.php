<?php

namespace App;

use App\Presenters\CommandPresenter;
use Illuminate\Database\Eloquent\Model;
use Robbo\Presenter\PresentableInterface;

/**
 * Model for templates.
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
    protected $visible = ['id', 'name', 'command_count', 'file_count', 'config_count'];

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
    protected $appends = ['command_count', 'file_count', 'config_count'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_template' => 'boolean',
    ];

    /**
     * Query scope to only show templates.
     *
     * @param  object $query
     * @return object
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
     * Has many relationship.
     *
     * @return Command
     */
    public function commands()
    {
        return $this->hasMany('App\Command', 'project_id');
    }

    /**
     * Has many relationship.
     *
     * @return SharedFile
     */
    public function sharedFiles()
    {
        return $this->hasMany('App\SharedFile', 'project_id');
    }

    /**
     * Has many relationship to project file.
     *
     * @return ProjectFile
     */
    public function projectFiles()
    {
        return $this->hasMany('App\ProjectFile', 'project_id');
    }

    /**
     * Gets the view presenter.
     *
     * @return ProjectPresenter
     */
    public function getPresenter()
    {
        return new CommandPresenter($this);
    }
}
