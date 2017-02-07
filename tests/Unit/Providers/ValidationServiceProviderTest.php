<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Providers;

use Illuminate\Validation\Factory as ValidatorFactory;
use Mockery as m;
use REBELinBLUE\Deployer\Providers\ValidationServiceProvider;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Validators\ValidatorInterface;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Providers\ValidationServiceProvider
 */
class ValidationServiceProviderTest extends TestCase
{
    /**
     * @xcover ::boot
     */
    public function testBoot()
    {
        $factory = m::mock(ValidatorFactory::class);

        $factory->shouldReceive('extend')
                ->times(4)
                ->with(m::type('string'), m::on(function ($validator) {
                    $class = str_replace('@validate', '', $validator);

                    $instance = m::mock($class);
                    $this->assertInstanceOf(ValidatorInterface::class, $instance);
                    $this->assertStringEndsWith('@validate', $validator);

                    return ($instance instanceof ValidatorInterface);
                }));

        $this->app->instance('validator', $factory);

        $provider = new ValidationServiceProvider($this->app);
        $provider->boot();
    }
}
