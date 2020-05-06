<?php

namespace REBELinBLUE\Deployer\Tests\Unit\Services\Filesystem;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use REBELinBLUE\Deployer\Services\Filesystem\Filesystem;
use REBELinBLUE\Deployer\Tests\TestCase;

/**
 * @coversDefaultClass \REBELinBLUE\Deployer\Services\Filesystem\Filesystem
 */
class FilesystemTest extends TestCase
{
    protected $longPathNamesWindows = [];

    /**
     * @var Filesystem
     */
    protected $filesystem = null;

    /**
     * @var string
     */
    protected $workspace = null;

    // Copied from https://github.com/symfony/filesystem/blob/master/Tests/FilesystemTestCase.php
    // which this test used to extend
    private $umask;

    /**
     * @var bool|null Flag for hard links on Windows
     */
    private static $linkOnWindows = null;

    /**
     * @var bool|null Flag for symbolic links on Windows
     */
    private static $symlinkOnWindows = null;

    public static function setUpBeforeClass(): void
    {
        if ('\\' === \DIRECTORY_SEPARATOR) {
            self::$linkOnWindows = true;

            $originFile = tempnam(sys_get_temp_dir(), 'li');
            $targetFile = tempnam(sys_get_temp_dir(), 'li');

            if (true !== @link($originFile, $targetFile)) {
                $report = error_get_last();

                if (\is_array($report) && false !== strpos($report['message'], 'error code(1314)')) {
                    self::$linkOnWindows = false;
                }
            } else {
                @unlink($targetFile);
            }

            self::$symlinkOnWindows = true;

            $originDir = tempnam(sys_get_temp_dir(), 'sl');
            $targetDir = tempnam(sys_get_temp_dir(), 'sl');

            if (true !== @symlink($originDir, $targetDir)) {
                $report = error_get_last();

                if (\is_array($report) && false !== strpos($report['message'], 'error code(1314)')) {
                    self::$symlinkOnWindows = false;
                }
            } else {
                @unlink($targetDir);
            }
        }
    }

    protected function setUp(): void
    {
        $this->umask = umask(0);

        $this->filesystem = new Filesystem();
        $this->workspace  = sys_get_temp_dir() . '/' . microtime(true) . '.' . mt_rand();

        mkdir($this->workspace, 0777, true);

        $this->workspace = realpath($this->workspace);
    }

    protected function tearDown(): void
    {
        if (!empty($this->longPathNamesWindows)) {
            foreach ($this->longPathNamesWindows as $path) {
                exec('DEL ' . $path);
            }

            $this->longPathNamesWindows = [];
        }

        $this->filesystem->delete($this->workspace);

        umask($this->umask);
    }

    /**
     * @covers ::tempnam
     */
    public function testTempnam()
    {
        $filename = $this->filesystem->tempnam($this->workspace);

        $this->assertFileExists($filename);
    }

    /**
     * @covers ::tempnam
     */
    public function testTempnamUsesPrefix()
    {
        $prefix = 'foo';

        $filename = $this->filesystem->tempnam($this->workspace, $prefix);

        $this->assertStringStartsWith($this->workspace . DIRECTORY_SEPARATOR . $prefix, $filename);
        $this->assertFileExists($filename);
    }

    /**
     * @covers ::tempnam
     */
    public function testTempnamThrowsFileNotFoundExceptionWhenDirectoryDoesNotExist()
    {
        $this->expectException(FileNotFoundException::class);

        $this->filesystem->tempnam($this->workspace . DIRECTORY_SEPARATOR . 'dir-does-not-exist');
    }

    /**
     * @covers ::touch
     */
    public function testTouch()
    {
        $file = $this->workspace . DIRECTORY_SEPARATOR . 'file-to-touch';

        $result = $this->filesystem->touch($file);

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

        $result = $this->filesystem->md5($file);

        $this->assertSame('d9e367e2fffda3d65d669dc4f3f7780b', $result);
    }

    /**
     * @covers ::md5
     */
    public function testMd5ThrowsExceptionWhenFileDoesNotExist()
    {
        $this->expectException(FileNotFoundException::class);

        $this->filesystem->md5($this->workspace . DIRECTORY_SEPARATOR . 'file-does-not-exist');
    }
}
