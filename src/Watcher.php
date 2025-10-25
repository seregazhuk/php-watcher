<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher;

use React\EventLoop\LoopInterface;
use seregazhuk\PhpWatcher\Config\WatchList;
use seregazhuk\PhpWatcher\Filesystem\ChangesListener\ChangesListenerInterface;

final class Watcher
{
    public function __construct(private readonly LoopInterface $loop, private readonly ChangesListenerInterface $filesystemListener) {}

    public function startWatching(
        ProcessRunner $processRunner,
        WatchList $watchList,
        int $signal,
        float $delayToRestart
    ): void {
        $processRunner->start();
        $this->filesystemListener->start($watchList);
        $this->filesystemListener->onChange(
            static function () use ($processRunner, $signal, $delayToRestart): void {
                $processRunner->stop($signal);
                $processRunner->restart($delayToRestart);
            }
        );

        $this->loop->run();
    }
}
