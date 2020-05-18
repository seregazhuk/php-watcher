<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Filesystem;

use Evenement\EventEmitter;
use React\ChildProcess\Process;
use React\EventLoop\LoopInterface;
use RuntimeException;
use seregazhuk\PhpWatcher\Config\WatchList;

final class ChangesListener extends EventEmitter
{
    private const INTERVAL = 0.15;

    private $loop;

    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
    }

    public function start(WatchList $watchList): void
    {
        $watcher = ResourceWatcherBuilder::create($watchList);

        $this->loop->addPeriodicTimer(
            self::INTERVAL,
            function () use ($watcher) {
                if ($watcher->findChanges()->hasChanges()) {
                    $this->emit('change');
                }
            }
        );
    }
}
