<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Stubs;

use REBELinBLUE\Deployer\User;

class StubUser extends User
{
    public function save(array $options = [])
    {
        // Overwrite the save method which is called by requestEmailToken, so we don't need to worry about migrations
    }
}
