<?php

namespace App;

/**
 * Model for templates
 */
class Template extends Project
{
    /**
     * Fields to show in the JSON presentation
     *
     * @var array
     */
    protected $visible = ['id', 'name', 'command_count'];

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
    protected $appends = ['command_count'];

    /**
     * Override the boot method to bind model event listeners.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        // FIXME: Horrible hack
        // Clear data not needed in templates
        static::created(function (Template $model) {
            $model->private_key = '';
            $model->public_key = '';
            $model->hash = '';
            $model->save();
        });
    }

    /**
     * Query scope to only show templates
     *
     * @param object $query
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
     * Has many relationship.
     *
     * @return Command
     */
    public function commands()
    {
        return $this->hasMany('App\Command', 'project_id');
    }
}
