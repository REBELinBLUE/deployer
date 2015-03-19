<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class DeployStep extends Model
{
    public function servers()
    {
        return $this->hasMany('App\ServerLog');
    }

    public function command()
    {
        return $this->belongsTo('App\Command');
    }
}
