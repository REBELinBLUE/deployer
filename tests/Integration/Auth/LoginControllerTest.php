<?php

namespace REBELinBLUE\Deployer\Tests\Integration\Auth;

use Illuminate\Foundation\Testing\DatabaseMigrations;

use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Http\Controllers\Auth\LoginController
 */
class LoginControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testAuthenticationRequired()
    {
        $response = $this->get('/');

        $response->assertStatus(302)
                 ->assertRedirect('/login');
    }
}
