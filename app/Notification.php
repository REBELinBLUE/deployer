<?php namespace App;

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
            'text' => 'This is a test to ensure the notification is ' .
                      'setup correctly, if you can see this it means it is! :+1:'
        ];
    }
}
