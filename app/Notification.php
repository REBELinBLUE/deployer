<?php namespace App;

use Lang;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    public function testPayload()
    {
        return [
            'text' => Lang::get('notification.test_message')
        ];
    }
}
