<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Events\Listeners;

use Carbon\Carbon;
use Illuminate\Auth\Events\Login;
use Illuminate\Session\Store;
use Mockery as m;
use REBELinBLUE\Deployer\Events\Listeners\CreateJwt;
use REBELinBLUE\Deployer\Services\Token\TokenGeneratorInterface;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\User;
use Tymon\JWTAuth\JWTAuth;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Events\Listeners\CreateJwt
 */
class CreateJwtTest extends TestCase
{
    /**
     * @covers ::handle
     * @covers ::__construct
     */
    public function testHandleGeneratesJwt()
    {
        Carbon::setTestNow(Carbon::create(2017, 5, 21, 12, 00, 00, 'UTC'));

        $random    = 'a-generated-token';
        $userId    = 1;
        $timestamp = 1495368000;
        $expires   = 1495378800;
        $token     = 'YS1nZW5lcmF0ZWQtdG9rZW4='; // Base64 encoded 'a-generated-token'

        $expected = [
            'iat'  => $timestamp,
            'jti'  => $token,
            'iss'  => config('app.url'),
            'nbf'  => $timestamp,
            'exp'  => $expires,
            'data' => [
                'userId' => $userId,
            ],
        ];

        $user = m::mock(User::class);
        $user->shouldReceive('getAttribute')->with('id')->once()->andReturn($userId);

        $session = m::mock(Store::class);
        $session->shouldReceive('put')->once()->with('jwt', $expected);

        $auth = m::mock(JWTAuth::class);
        $auth->shouldReceive('fromUser')->once()->with($user, $expected)->andReturn($expected);

        $generator = m::mock(TokenGeneratorInterface::class);
        $generator->shouldReceive('generateRandom')->once()->with(m::type('int'))->andReturn($random);

        $listener = new CreateJwt($auth, $session, $generator);
        $listener->handle(new Login($user, false));
    }
}
