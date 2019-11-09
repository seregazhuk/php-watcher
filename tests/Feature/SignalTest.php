<?php declare(strict_types=1);

namespace tests\Feature;

use tests\Feature\Helper\WatcherTestCase;
use tests\Helper\Filesystem;

final class SignalTest extends WatcherTestCase
{
    /** @test */
    public function it_sends_a_specified_signal_to_restart_the_app(): void
    {
        if (!defined('SIGTERM')) {
            $this->markTestSkipped('SIGTERM is not defined');
        }

        $scriptToRun = Filesystem::createHelloWorldPHPFileWithSignalsHandling();
        $this->watch($scriptToRun, ['--signal', 'SIGTERM', '--watch', __DIR__]);
        $this->wait();

        Filesystem::createHelloWorldPHPFile();
        $this->wait();

        $this->assertOutputContains(SIGTERM . ' signal was received');
    }
}
