<?php declare(strict_types=1);

namespace tests;

use tests\Helper\Filesystem;
use tests\Helper\WatcherRunner;
use tests\Helper\WatcherTestCase;

final class SignalTest extends WatcherTestCase
{
    /** @test */
    public function it_sends_a_specified_signal_to_restart_the_app(): void
    {
        if (!defined('SIGTERM')) {
            $this->markTestSkipped('SIGTERM is not defined');
        }

        $scriptToRun = Filesystem::createHelloWorldPHPFileWithSignalsHandling();
        $watcher = (new WatcherRunner)->run($scriptToRun, ['--signal', 'SIGTERM', '--watch', __DIR__]);
        $this->wait();

        Filesystem::createHelloWorldPHPFile();
        $this->wait();

        $output = $watcher->getOutput();

        $this->assertStringContainsString(SIGTERM . ' signal was received', $output);
    }
}
