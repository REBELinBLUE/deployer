<?php

namespace REBELinBLUE\Deployer\Traits;

use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\ProjectFile;
use REBELinBLUE\Deployer\SharedFile;
use REBELinBLUE\Deployer\Variable;

/**
 * A trait for project relationships
 */
trait ProjectRelations
{
    /**
     * Has many relationship.
     *
     * @return Command
     */
    public function commands()
    {
        return $this->morphMany(Command::class, 'target')
                    ->orderBy('order', 'ASC');
    }
    /**
     * Has many relationship.
     *
     * @return Variable
     */
    public function variables()
    {
        return $this->morphMany(Variable::class, 'target');
    }
    /**
     * Has many relationship.
     *
     * @return SharedFile
     */
    public function sharedFiles()
    {
        return $this->morphMany(SharedFile::class, 'target');
    }
    /**
     * Has many relationship to project file.
     *
     * @return ProjectFile
     */
    public function projectFiles()
    {
        return $this->morphMany(ProjectFile::class, 'target');
    }
}
