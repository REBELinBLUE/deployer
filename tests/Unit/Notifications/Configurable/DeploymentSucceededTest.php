<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Notifications\Configurable;

use Illuminate\Support\Facades\Lang;
use Mockery as m;
use REBELinBLUE\Deployer\Deployment;
use REBELinBLUE\Deployer\Notifications\Configurable\DeploymentSucceeded;
use REBELinBLUE\Deployer\Notifications\Notification;
use REBELinBLUE\Deployer\Project;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Notifications\Configurable\DeploymentSucceeded
 */
class DeploymentSucceededTest extends DeploymentFinishedTestCase
{
    /**
     * @covers ::__construct
     */
    public function testExtendsNotification()
    {
        $project    = m::mock(Project::class);
        $deployment = m::mock(Deployment::class);

        $notification = new DeploymentSucceeded($project, $deployment);

        $this->assertInstanceOf(Notification::class, $notification);
    }

    /**
     * @covers ::toTwilio
     * @covers \REBELinBLUE\Deployer\Notifications\Configurable\DeploymentFinished::buildTwilioMessage
     */
    public function testToTwilio()
    {
        $this->toTwilio(DeploymentSucceeded::class, 'deployments.success_sms_message');
    }

    /**
     * @covers ::toWebhook
     * @covers \REBELinBLUE\Deployer\Notifications\Configurable\DeploymentFinished::buildWebhookMessage
     */
    public function testToWebhook()
    {
        $this->toWebhook(DeploymentSucceeded::class, 'success', 'deployment_succeeded');
    }

    /**
     * @covers ::toMail
     * @covers \REBELinBLUE\Deployer\Notifications\Configurable\DeploymentFinished::buildMailMessage
     */
    public function testToMail()
    {
        $this->toMail(
            DeploymentSucceeded::class,
            'deployments.success_email_subject',
            'deployments.success_email_message',
            'success'
        );
    }

    /**
     * @covers ::toMail
     * @covers \REBELinBLUE\Deployer\Notifications\Configurable\DeploymentFinished::buildMailMessage
     */
    public function testToMailWithReason()
    {
        $this->toMail(
            DeploymentSucceeded::class,
            'deployments.success_email_subject',
            'deployments.success_email_message',
            'success',
            true
        );
    }

    /*
     * @covers ::toSlack
     * @covers \REBELinBLUE\Deployer\Notifications\Configurable\DeploymentFinished::buildSlackMessage
     */
    public function testToSlack()
    {
        $this->toSlack(DeploymentSucceeded::class, 'deployments.success_slack_message', 'success');
    }

    /*
     * @covers ::toSlack
     * @covers \REBELinBLUE\Deployer\Notifications\Configurable\DeploymentFinished::buildSlackMessage
     */
    public function testToSlackWithCommitUrl()
    {
        $this->toSlack(DeploymentSucceeded::class, 'deployments.success_slack_message', 'success', false);
    }

    /**
     * @covers ::toHipchat
     * @covers \REBELinBLUE\Deployer\Notifications\Configurable\DeploymentFinished::buildHipchatMessage
     */
    public function testToHipchat()
    {
        $this->toHipchat(DeploymentSucceeded::class, 'deployments.success_hipchat_message', 'success');
    }
}
