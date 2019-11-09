<?php declare(strict_types=1);


namespace tests\Unit;


use PHPUnit\Framework\TestCase;
use React\EventLoop\Factory;
use seregazhuk\PhpWatcher\Config\WatchList;
use seregazhuk\PhpWatcher\Filesystem\ChangesListener;
use tests\Helper\Filesystem;
use tests\Helper\WithFilesystem;
use function \Clue\React\Block\sleep;

final class ChangesListenerTest extends TestCase
{
    use WithFilesystem;

    /** @test */
    public function it_emits_change_event_on_changes(): void
    {
        $loop = Factory::create();
        $listener = new ChangesListener($loop, new WatchList([Filesystem::fixturesDir()]));
        $listener->start();

        $loop->addTimer(1, [Filesystem::class, 'createHelloWorldPHPFile']);
        $eventWasEmitted = false;
        $listener->on('change', function () use (&$eventWasEmitted) {
            $eventWasEmitted = true;
        });
        sleep(3, $loop);

        $this->assertTrue($eventWasEmitted, '"change" event should be emitted');
    }
}
