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
        return $this->belongsTo(Group::class);
    }

    /**
     * Has many relationship.
     *
     * @return Server
     */
    public function servers()
    {
        return $this->hasMany(Server::class)
                    ->orderBy('order', 'ASC');
    }

    /**
     * Has many relationship.
     *
     * @return Heartbeat
     */
    public function heartbeats()
    {
        return $this->hasMany(Heartbeat::class)
                    ->orderBy('name');
    }

    /**
     * Has many relationship.
     *
     * @return Notification
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class)
                    ->orderBy('name');
    }

    /**
     * Has many relationship.
     *
     * @return Deployment
     */
    public function deployments()
    {
        return $this->hasMany(Deployment::class)
                    ->orderBy('started_at', 'DESC');
    }

    /**
     * Has many relationship.
     *
     * @return Command
     */
    public function commands()
    {
        return $this->hasMany(Command::class)
                    ->orderBy('order', 'ASC');
    }

    /**
     * Has many relationship.
     *
     * @return Variable
     */
    public function variables()
    {
        return $this->hasMany(Variable::class);
    }

    /**
     * Has many relationship.
     *
     * @return SharedFile
     */
    public function sharedFiles()
    {
        return $this->hasMany(SharedFile::class);
    }

    /**
     * Has many relationship to project file.
     *
     * @return ProjectFile
     */
    public function projectFiles()
    {
        return $this->hasMany(ProjectFile::class);
    }

    /**
     * Has many relationship.
     *
     * @return SharedFile
     */
    public function notifyEmails()
    {
        return $this->hasMany(NotifyEmail::class);
    }

    /**
     * Has many urls to check.
     *
     * @return CheckUrl
     */
    public function checkUrls()
    {
        return $this->hasMany(CheckUrl::class);
    }
}
