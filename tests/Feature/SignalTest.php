<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use seregazhuk\PhpWatcher\Tests\Feature\Helper\Filesystem;
use seregazhuk\PhpWatcher\Tests\Feature\Helper\WatcherTestCase;

final class SignalTest extends WatcherTestCase
{
    #[Test]
    public function it_sends_a_specified_signal_to_restart_the_app(): void
    {
        if (! defined('SIGTERM') || ! extension_loaded('pcntl')) {
            $this->markTestSkipped('SIGTERM is not defined');
        }

        $scriptToRun = Filesystem::createHelloWorldPHPFileWithSignalsHandling();
        $this->watch($scriptToRun, ['--watch', Filesystem::fixturesDir()]);
        $this->wait();

        Filesystem::createHelloWorldPHPFile();
        $this->wait();

        $this->assertOutputContains(SIGTERM.' restarting due to change');
    }
}
