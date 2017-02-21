<?php

namespace REBELinBLUE\Deployer\Tests\Unit\stubs;

use REBELinBLUE\Deployer\Project as BaseProject;

/**
 * A stub class to make the protected methods public for testing.
 */
class Project extends BaseProject
{
    public function generateSSHKey()
    {
        parent::generateSSHKey();
    }

    public function regeneratePublicKey()
    {
        parent::regeneratePublicKey();
    }
}
