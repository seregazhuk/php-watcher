<?php declare(strict_types=1);

namespace tests;

use tests\Helper\Filesystem;
use tests\Helper\WatcherRunner;
use tests\Helper\WatcherTestCase;

final class RunScriptTest extends WatcherTestCase
{
    /** @test */
    public function it_runs_a_php_script(): void
    {
        $scriptToRun = Filesystem::createHelloWorldPHPFile();
        $watcher = (new WatcherRunner)->run($scriptToRun);

        $this->wait();
        $output = $watcher->getOutput();

        $this->assertStringContainsString("starting `php $scriptToRun`", $output);
        $this->assertStringContainsString('Hello, world', $output);
    }

    /** @test */
    public function a_script_restarts_by_sending_a_sigterm_signal(): void
    {
        $scriptToRun = Filesystem::createHelloWorldPHPFileWithSignalsHandling();
        $watcher = (new WatcherRunner)->run($scriptToRun, ['--watch', $scriptToRun]);
        $this->wait();

        Filesystem::appendFileContentsWith($scriptToRun, ' ');
        $this->wait();
        $output = $watcher->getOutput();

        $this->assertStringContainsString('Hello, world', $output);
        $this->assertStringContainsString('SIGTERM was received', $output);
    }
}
