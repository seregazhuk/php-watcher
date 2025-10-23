<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Tests\Unit;

use PHPUnit\Framework\TestCase;
use seregazhuk\PhpWatcher\Filesystem\WatchPath;

final class WatchPathTest extends TestCase
{
    /** @test */
    public function it_provides_a_directory_path_for_a_file(): void
    {
        $path = new WatchPath('/root/test/file.txt');
        $this->assertEquals('/root/test', $path->path());
    }

    /** @test */
    public function it_provides_a_directory_path_for_a_directory(): void
    {
        $path = new WatchPath('/root/test');
        $this->assertEquals('/root', $path->path());
    }

    /** @test */
    public function it_provides_a_filename_for_a_file(): void
    {
        $path = new WatchPath('/root/test.txt');
        $this->assertEquals('test.txt', $path->fileName());
    }

    /** @test */
    public function it_provides_a_pattern_path_for_a_pattern(): void
    {
        $path = new WatchPath('/root/test.*');
        $this->assertEquals('test.*', $path->fileName());
    }

    /** @test */
    public function it_can_detect_pattern_or_a_file(): void
    {
        $path = new WatchPath('/root/test.*');
        $this->assertTrue($path->isFileOrPattern());

        $path = new WatchPath('/root/test.txt');
        $this->assertTrue($path->isFileOrPattern());

        $path = new WatchPath('/root/*.txt');
        $this->assertTrue($path->isFileOrPattern());

        $path = new WatchPath(__DIR__);
        $this->assertFalse($path->isFileOrPattern());
    }
}
