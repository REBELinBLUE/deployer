<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Database;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use REBELinBLUE\Deployer\Command;
use REBELinBLUE\Deployer\ConfigFile;
use REBELinBLUE\Deployer\SharedFile;
use REBELinBLUE\Deployer\Template;
use REBELinBLUE\Deployer\Tests\TestCase;
use REBELinBLUE\Deployer\Variable;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Template
 * @group slow
 */
class TemplateTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @covers ::getCommandCountAttribute
     */
    public function testGetCommandCountAttribute()
    {
        $expected = 2;

        /** @var Template $template */
        $template = factory(Template::class)->create();

        factory(Command::class, $expected)->make()->each(function ($command) use ($template) {
            $template->commands()->save($command);
        });

        // FIXME: We may not need the count attributes, laravel has this built in apparently?
        $this->assertSame($expected, $template->command_count);
        $this->assertSame($expected, $template->getCommandCountAttribute());
    }

    /**
     * @covers ::getFileCountAttribute
     */
    public function testGetFileCountAttribute()
    {
        $expected = 2;

        /** @var Template $template */
        $template = factory(Template::class)->create();
        factory(SharedFile::class, $expected)->make()->each(function ($sharedFile) use ($template) {
            $template->sharedFiles()->save($sharedFile);
        });

        $this->assertSame($expected, $template->file_count);
        $this->assertSame($expected, $template->getFileCountAttribute());
    }

    /**
     * @covers ::getConfigCountAttribute
     */
    public function testGetConfigCountAttribute()
    {
        $expected = 2;

        /** @var Template $template */
        $template = factory(Template::class)->create();
        factory(ConfigFile::class, $expected)->make()->each(function ($configFile) use ($template) {
            $template->configFiles()->save($configFile);
        });

        $this->assertSame($expected, $template->config_count);
        $this->assertSame($expected, $template->getConfigCountAttribute());
    }

    /**
     * @covers ::getVariableCountAttribute
     */
    public function testGetVariableCountAttribute()
    {
        $expected = 2;

        /** @var Template $template */
        $template = factory(Template::class)->create();
        factory(Variable::class, $expected)->make()->each(function ($variables) use ($template) {
            $template->variables()->save($variables);
        });

        $this->assertSame($expected, $template->variable_count);
        $this->assertSame($expected, $template->getVariableCountAttribute());
    }
}
