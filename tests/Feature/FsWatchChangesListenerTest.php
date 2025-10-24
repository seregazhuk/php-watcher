<?php

declare(strict_types=1);

namespace Feature;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use React\EventLoop\Loop;
use seregazhuk\PhpWatcher\Config\WatchList;
use seregazhuk\PhpWatcher\Filesystem\ChangesListener\FSWatchChangesListener;
use seregazhuk\PhpWatcher\Tests\Feature\Helper\Filesystem;
use seregazhuk\PhpWatcher\Tests\Feature\Helper\WithFilesystem;

use Symfony\Component\Process\Process;

use function React\Async\delay;

final class FsWatchChangesListenerTest extends TestCase
{
    use WithFilesystem;

    #[Test]
    public function it_emits_change_event_on_changes(): void
    {
        if (! FSWatchChangesListener::isAvailable()) {
            $this->markTestSkipped('fswatch is not available');
        }

        $loop = Loop::get();
        $listener = new FSWatchChangesListener;

        $eventWasEmitted = false;
        $listener->onChange(function () use (&$eventWasEmitted): void {
            $eventWasEmitted = true;
        });
        $loop->addTimer(1, Filesystem::createHelloWorldPHPFile(...));

        $listener->start(new WatchList([__DIR__ . '/../../' .Filesystem::fixturesDir()]));
        delay(3);
        $loop->addTimer(4, fn () => $loop->stop());
        $this->assertTrue($eventWasEmitted, '"change" event should be emitted');
    }
}
