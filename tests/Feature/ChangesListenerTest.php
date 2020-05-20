<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Tests\Feature;

use PHPUnit\Framework\TestCase;
use React\EventLoop\Factory;
use seregazhuk\PhpWatcher\Config\WatchList;
use seregazhuk\PhpWatcher\Filesystem\ResourceWatcherBased\ChangesListener;
use seregazhuk\PhpWatcher\Tests\Feature\Helper\Filesystem;
use seregazhuk\PhpWatcher\Tests\Feature\Helper\WithFilesystem;

use function Clue\React\Block\sleep;

final class ChangesListenerTest extends TestCase
{
    use WithFilesystem;

    /** @test */
    public function it_emits_change_event_on_changes(): void
    {
        $loop = Factory::create();
        $listener = new ChangesListener(new WatchList([Filesystem::fixturesDir()]), $loop);
        $listener->start();

        $loop->addTimer(1, [Filesystem::class, 'createHelloWorldPHPFile']);
        $eventWasEmitted = false;
        $listener->on('change', static function () use (&$eventWasEmitted) {
            $eventWasEmitted = true;
        });
        sleep(4, $loop); // to be sure changes have been detected

        $this->assertTrue($eventWasEmitted, '"change" event should be emitted');
    }
}
