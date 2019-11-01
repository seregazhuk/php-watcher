<?php declare(strict_types=1);

namespace tests;

use tests\Helper\Filesystem;
use tests\Helper\WatcherRunner;
use tests\Helper\WatcherTestCase;

final class ScriptExitTest extends WatcherTestCase
{
    /** @test */
    public function it_detects_when_script_exits(): void
    {
        $fileToWatch = Filesystem::createHelloWorldPHPFile();
        $watcher = (new WatcherRunner)->run($fileToWatch, ['--watch', $fileToWatch]);
        $this->wait();

        $this->assertStringContainsString('clean exit - waiting for changes before restart', $watcher->getOutput());
    }

    /** @test */
    public function it_detects_when_script_crashes(): void
    {
        $fileToWatch = Filesystem::createPHPFileThatCrashes();
        $watcher = (new WatcherRunner)->run($fileToWatch, ['--watch', $fileToWatch]);
        $this->wait();

        $this->assertStringContainsString('app crashed - waiting for file changes before starting', $watcher->getOutput());
    }
}
