<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Services\Filesystem;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Tests\FilesystemTestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Services\Filesystem\Filesystem
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
     * @covers ::touch
     */
    public function testTouch()
    {
        $file = $this->workspace . DIRECTORY_SEPARATOR . 'file-to-touch';

        $filesystem = new Filesystem();
        $result     = $filesystem->touch($file);

        $this->assertTrue($result);
        $this->assertFileExists($file);
    }

    /**
     * @covers ::md5
     */
    public function testMd5()
    {
        $content = 'some test content';
        $file    = $this->workspace . DIRECTORY_SEPARATOR . 'file-with-content';

        if (!file_put_contents($file, $content)) {
            $this->markTestSkipped('Could not create test file');
        }

        $filesystem = new Filesystem();
        $result     = $filesystem->md5($file);

        $this->assertSame(md5($content), $result);
    }

    /**
     * @covers ::md5
     */
    public function testMd5ThrowsExceptionWhenFileDoesNotExist()
    {
        $this->expectException(FileNotFoundException::class);

        $filesystem = new Filesystem();
        $filesystem->md5($this->workspace . DIRECTORY_SEPARATOR . 'file-does-not-exist');
    }
}
