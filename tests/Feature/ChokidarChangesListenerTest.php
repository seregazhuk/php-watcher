<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use React\EventLoop\Loop;
use seregazhuk\PhpWatcher\Config\WatchList;
use seregazhuk\PhpWatcher\Filesystem\ChangesListener\ChokidarChangesListener;
use seregazhuk\PhpWatcher\Tests\Feature\Helper\Filesystem;
use seregazhuk\PhpWatcher\Tests\Feature\Helper\WithFilesystem;

use function React\Async\delay;

final class ChokidarChangesListenerTest extends TestCase
{
    use WithFilesystem;

    #[Test]
    public function it_emits_change_event_on_changes(): void
    {
        $loop = Loop::get();
        $listener = new ChokidarChangesListener($loop);
        $listener->start(new WatchList([Filesystem::fixturesDir()]));

        $loop->addTimer(1, Filesystem::createHelloWorldPHPFile(...));
        $eventWasEmitted = false;
        $listener->onChange(function () use (&$eventWasEmitted): void {
            $eventWasEmitted = true;
        });
        delay(4);
        $this->assertTrue($eventWasEmitted, '"change" event should be emitted');
        $listener->stop();
    }
}
