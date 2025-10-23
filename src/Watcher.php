<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher;

use seregazhuk\PhpWatcher\Config\WatchList;

final class Watcher
{
    public function __construct(private readonly \React\EventLoop\LoopInterface $loop, private readonly Filesystem\ChangesListener $filesystemListener) {}

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
