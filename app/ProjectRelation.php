<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Abstract class to hold the relationships for projects to stop PHPMD complaning
 * This seems like such a hacky way to structure it.
 */
abstract class ProjectRelation extends Model
{
    /**
     * Belongs to relationship.
     *
     * @return Group
     */
    public function group()
    {
        return $this->belongsTo('App\Group');
    }

    /**
     * Has many relationship.
     *
     * @return Server
     */
    public function servers()
    {
        return $this->hasMany('App\Server')->orderBy('order', 'ASC');
    }

    /**
     * Has many relationship.
     *
     * @return Heartbeat
     */
    public function heartbeats()
    {
        return $this->hasMany('App\Heartbeat')->orderBy('name');
    }

    /**
     * Has many relationship.
     *
     * @return Notification
     */
    public function notifications()
    {
        return $this->hasMany('App\Notification')->orderBy('name');
    }

    /**
     * Has many relationship.
     *
     * @return Deployment
     */
    public function deployments()
    {
        return $this->hasMany('App\Deployment')->orderBy('started_at', 'DESC');
    }

    /**
     * Has many relationship.
     *
     * @return Command
     */
    public function commands()
    {
        return $this->hasMany('App\Command')->orderBy('order', 'ASC');
    }

    /**
     * Has many relationship.
     *
     * @return SharedFile
     */
    public function sharedFiles()
    {
        return $this->hasMany('App\SharedFile');
    }

    /**
     * Has many relationship to project file.
     *
     * @return ProjectFile
     */
    public function projectFiles()
    {
        return $this->hasMany('App\ProjectFile');
    }

    /**
     * Has many relationship.
     *
     * @return SharedFile
     */
    public function notifyEmails()
    {
        return $this->hasMany('App\NotifyEmail');
    }

    /**
     * Has many urls to check.
     *
     * @return CheckUrl
     */
    public function checkUrls()
    {
        return $this->hasMany('App\CheckUrl');
    }
}
