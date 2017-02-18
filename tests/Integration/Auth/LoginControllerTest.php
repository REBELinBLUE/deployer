<?php

namespace REBELinBLUE\Deployer\Tests\Integration\Auth;

use Illuminate\Foundation\Testing\DatabaseTransactions;

use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Http\Controllers\Auth\LoginController
 */
class LoginControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testAuthenticationRequired()
    {
        $response = $this->get('/');

        $response->assertStatus(302)
                 ->assertRedirect('/login');
    }
}
