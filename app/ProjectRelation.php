<?php

namespace REBELinBLUE\Deployer;

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
        return $this->belongsTo('REBELinBLUE\Deployer\Group');
    }

    /**
     * Has many relationship.
     *
     * @return Server
     */
    public function servers()
    {
        return $this->hasMany('REBELinBLUE\Deployer\Server')->orderBy('order', 'ASC');
    }

    /**
     * Has many relationship.
     *
     * @return Heartbeat
     */
    public function heartbeats()
    {
        return $this->hasMany('REBELinBLUE\Deployer\Heartbeat')->orderBy('name');
    }

    /**
     * Has many relationship.
     *
     * @return Notification
     */
    public function notifications()
    {
        return $this->hasMany('REBELinBLUE\Deployer\Notification')->orderBy('name');
    }

    /**
     * Has many relationship.
     *
     * @return Deployment
     */
    public function deployments()
    {
        return $this->hasMany('REBELinBLUE\Deployer\Deployment')->orderBy('started_at', 'DESC');
    }

    /**
     * Has many relationship.
     *
     * @return Command
     */
    public function commands()
    {
        return $this->hasMany('REBELinBLUE\Deployer\Command')->orderBy('order', 'ASC');
    }

    /**
     * Has many relationship.
     *
     * @return Variable
     */
    public function variables()
    {
        return $this->hasMany('REBELinBLUE\Deployer\Variable');
    }

    /**
     * Has many relationship.
     *
     * @return SharedFile
     */
    public function sharedFiles()
    {
        return $this->hasMany('REBELinBLUE\Deployer\SharedFile');
    }

    /**
     * Has many relationship to project file.
     *
     * @return ProjectFile
     */
    public function projectFiles()
    {
        return $this->hasMany('REBELinBLUE\Deployer\ProjectFile');
    }

    /**
     * Has many relationship.
     *
     * @return SharedFile
     */
    public function notifyEmails()
    {
        return $this->hasMany('REBELinBLUE\Deployer\NotifyEmail');
    }

    /**
     * Has many urls to check.
     *
     * @return CheckUrl
     */
    public function checkUrls()
    {
        return $this->hasMany('REBELinBLUE\Deployer\CheckUrl');
    }
}
