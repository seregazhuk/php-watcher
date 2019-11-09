<?php declare(strict_types=1);

namespace tests\Feature;

use tests\Feature\Helper\WatcherTestCase;
use tests\Helper\Filesystem;

final class ScriptExitTest extends WatcherTestCase
{
    /** @test */
    public function it_detects_when_script_exits(): void
    {
        $fileToWatch = Filesystem::createHelloWorldPHPFile();
        $this->watch($fileToWatch, ['--watch', $fileToWatch]);
        $this->wait();

        $this->assertOutputContains('clean exit - waiting for changes before restart');
    }

    /** @test */
    public function it_detects_when_script_crashes(): void
    {
        $fileToWatch = Filesystem::createPHPFileThatCrashes();
        $this->watch($fileToWatch, ['--watch', $fileToWatch]);
        $this->wait();

        $this->assertOutputContains('app crashed - waiting for file changes before starting');
    }
}
