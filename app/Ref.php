<?php

namespace REBELinBLUE\Deployer;

use Illuminate\Database\Eloquent\Model;

/**
 * Git Ref model.
 *
 * @property integer $id
 * @property string $name
 * @property boolean $is_tag
 * @property integer $project_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read Project $project
 */
class Ref extends Model
{
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $visible = ['id', 'name', 'is_tag'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'project_id', 'is_tag'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_tag' => 'boolean',
    ];

    /**
     * Belongs to relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
