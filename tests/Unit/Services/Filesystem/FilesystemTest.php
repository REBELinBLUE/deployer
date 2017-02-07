<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Services\Filesystem;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Tests\FilesystemTestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Services\Filesystem\Filesystem
 * @fixme: using sshkey as the prefix in the code may not work for the clean up script as it won't match due to being
 * stripped to 3 characters?
 */
class FilesystemTest extends FilesystemTestCase
{
    /**
     * @covers ::tempnam
     */
    public function testTempnam()
    {
        $filesystem = new Filesystem();
        $filename   = $filesystem->tempnam($this->workspace);

        $this->assertFileExists($filename);
    }

    /**
     * @covers ::tempnam
     */
    public function testTempnamUsesPrefix()
    {
        $prefix = 'foo';

        $filesystem = new Filesystem();
        $filename   = $filesystem->tempnam($this->workspace, $prefix);

        $this->assertStringStartsWith($this->workspace . DIRECTORY_SEPARATOR . $prefix, $filename);
        $this->assertFileExists($filename);
    }

    /**
     * @covers ::tempnam
     */
    public function testTempnamThrowsFileNotFoundExceptionWhenDirectoryDoesNotExist()
    {
        $this->expectException(FileNotFoundException::class);

        $filesystem = new Filesystem();
        $filesystem->tempnam($this->workspace . DIRECTORY_SEPARATOR . 'dir-does-not-exist');
    }

    /**
     * @covers ::tempnam
     */
    public function testTempnamThrowsExceptionWhenTempFileCanNotBeCreated()
    {
        $this->markTestSkipped('Not sure how to test this, no easy way to force a failure');
        $this->expectException(IOException::class);

        $filesystem = new Filesystem();
        $filesystem->tempnam($this->workspace);
    }
}
