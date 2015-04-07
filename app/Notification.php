<?php namespace App;

use Lang;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Notification model
 */
class Notification extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'channel', 'webhook', 'project_id'];

    /**
     * Belongs to relationship
     *
     * @return Project
     */
    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    /**
     * Generates a test payload for Slack
     *
     * @return array
     */
    public function testPayload()
    {
        return [
            'text' => Lang::get('notifications.test_message')
        ];
    }
}
