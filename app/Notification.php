<?php namespace App;

use Lang;
use Illuminate\Database\Eloquent\Model;

/**
 * Notification model
 */
class Notification extends Model
{
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
