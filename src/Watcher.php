<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher;

use React\EventLoop\LoopInterface;
use seregazhuk\PhpWatcher\Config\WatchList;
use seregazhuk\PhpWatcher\Filesystem\ResourceWatcherBased\ChangesListener;

final class Watcher
{
    private $loop;

    private $filesystemListener;

    public function __construct(LoopInterface $loop, ChangesListener $filesystemListener)
    {
        $this->loop = $loop;
        $this->filesystemListener = $filesystemListener;
    }

    public function startWatching(
        ProcessRunner $processRunner,
        WatchList $watchList,
        int $signal,
        float $delayToRestart
    ): void {
        $processRunner->start();

        $this->filesystemListener->start($watchList);
        $this->filesystemListener->onChange(
            static function () use ($processRunner, $signal, $delayToRestart) {
                $processRunner->stop($signal);
                $processRunner->restart($delayToRestart);
            }
        );

        $this->loop->run();
    }
}
