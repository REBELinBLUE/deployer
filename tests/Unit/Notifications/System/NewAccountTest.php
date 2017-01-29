<?php

namespace REBELinBLUE\Deployer\Unit\Tests\Notifications\System;

use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Mockery as m;
use REBELinBLUE\Deployer\Notifications\System\NewAccount;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\User;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Notifications\System\NewAccount
 */
class NewAccountTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::via
     */
    public function testSendViaEmail()
    {
        $notification = new NewAccount('a-new-password');

        $this->assertSame(['mail'], $notification->via());
    }

    /**
     * @covers ::toMail
     */
    public function testToMail()
    {
        $expectedPassword = 'a-test-password';
        $expectedEmail    = 'bob.smith@example.com';
        $expectedName     = 'Bob Smith';
        $subject          = 'expected subject';
        $actionUrl        = 'action button url';
        $actionText       = 'the button text';
        $introLine1       = 'in line 1 of text';
        $introLine2       = 'in line 2 of text';
        $introLine3       = 'IN line 3 of text';

        $user = m::mock(User::class);
        $user->shouldReceive('getAttribute')->atLeast()->once()->with('name')->andReturn($expectedName);
        $user->shouldReceive('getAttribute')->atLeast()->once()->with('email')->andReturn($expectedEmail);

        Lang::shouldReceive('get')->with('emails.creation_subject')->andReturn($subject);
        Lang::shouldReceive('get')->with('emails.created')->andReturn($introLine1);
        Lang::shouldReceive('get')->with('emails.username', ['username' => $expectedEmail])->andReturn($introLine2);
        Lang::shouldReceive('get')->with('emails.password', ['password' => $expectedPassword])->andReturn($introLine3);
        Lang::shouldReceive('get')->with('emails.login_now')->andReturn($actionText);

        // Replace the URL generator so that we can get a known URL
        $mock = m::mock(UrlGenerator::class);
        $mock->shouldReceive('route')
             ->with('dashboard', [], true)
             ->andReturn($actionUrl);

        App::instance('url', $mock);

        $notification = new NewAccount($expectedPassword);
        $mail         = $notification->toMail($user);
        $actual       = $mail->toArray();

        $this->assertSame($subject, $actual['subject']);
        $this->assertSame($actionUrl, $actual['actionUrl']);
        $this->assertSame($actionText, $actual['actionText']);

        $this->assertSame(3, count($actual['introLines']));
        $this->assertSame($introLine1, $actual['introLines'][0]);
        $this->assertSame($introLine2, $actual['introLines'][1]);
        $this->assertSame($introLine3, $actual['introLines'][2]);

        $this->assertArrayHasKey('name', $mail->viewData);
        $this->assertSame($expectedName, $mail->viewData['name']);
    }
}
