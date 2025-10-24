<?php

declare(strict_types=1);

namespace Feature;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use React\ChildProcess\Process;
use React\EventLoop\Loop;
use seregazhuk\PhpWatcher\Config\WatchList;
use seregazhuk\PhpWatcher\Filesystem\ChangesListener\FSWatchChangesListener;
use seregazhuk\PhpWatcher\Tests\Feature\Helper\Filesystem;
use seregazhuk\PhpWatcher\Tests\Feature\Helper\WithFilesystem;

use function React\Async\async;
use function React\Async\delay;

final class FsWatchChangesListenerTest extends TestCase
{
    use WithFilesystem;

    #[Test]
    public function it_emits_change_event_on_changes(): void
    {
        $process = new Process("which fswatch");
        $process->start();
        delay(0.1);

        if ($process->getExitCode() !== 0) {
            $this->markTestSkipped('fswatch is not available');
        }

        $loop = Loop::get();
        $loop->addTimer(1, async(Filesystem::createHelloWorldPHPFile(...)));

        $listener = new FSWatchChangesListener;
        $listener->start(new WatchList([Filesystem::fixturesDir()]));

        $listener->onChange(function (): void {
            $this->assertTrue(true);
        });
        delay(4);
        $loop->stop();
    }
}
