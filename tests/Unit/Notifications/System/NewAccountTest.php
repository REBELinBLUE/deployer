<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Notifications\System;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Routing\UrlGenerator;
use Mockery as m;
use REBELinBLUE\Deployer\Notifications\System\NewAccount;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\User;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Notifications\System\NewAccount
 */
class NewAccountTest extends TestCase
{
    private $translator;

    public function setUp()
    {
        parent::setUp();

        $this->translator = m::mock(Translator::class);
    }

    /**
     * @covers ::__construct
     * @covers ::via
     */
    public function testSendViaEmail()
    {
        $notification = new NewAccount('a-new-password', $this->translator);

        $this->assertSame(['mail'], $notification->via());
    }

    /**
     * @covers ::__construct
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

        $this->translator->shouldReceive('trans')->with('emails.creation_subject')->andReturn($subject);
        $this->translator->shouldReceive('trans')->with('emails.created')->andReturn($introLine1);
        $this->translator->shouldReceive('trans')
                         ->with('emails.username', ['username' => $expectedEmail])
                         ->andReturn($introLine2);
        $this->translator->shouldReceive('trans')
                         ->with('emails.password', ['password' => $expectedPassword])
                         ->andReturn($introLine3);
        $this->translator->shouldReceive('trans')->with('emails.login_now')->andReturn($actionText);

        // Replace the URL generator so that we can get a known URL
        $mock = m::mock(UrlGenerator::class);
        $mock->shouldReceive('route')
             ->with('dashboard', [], true)
             ->andReturn($actionUrl);

        $this->app->instance('url', $mock);

        $notification = new NewAccount($expectedPassword, $this->translator);
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
