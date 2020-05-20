<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher;

use React\EventLoop\LoopInterface;
use seregazhuk\PhpWatcher\Filesystem\ChangesListener;

final class Watcher
{
    private LoopInterface $loop;

    private ChangesListener $filesystemListener;

    public function __construct(LoopInterface $loop, ChangesListener $filesystemListener)
    {
        $this->loop = $loop;
        $this->filesystemListener = $filesystemListener;
    }

    public function startWatching(
        ProcessRunner $processRunner,
        int $signal,
        float $delayToRestart
    ): void {
        $processRunner->start();

        $this->filesystemListener->start();
        $this->filesystemListener->onChange(
            static function () use ($processRunner, $signal, $delayToRestart) {
                $processRunner->stop($signal);
                $processRunner->restart($delayToRestart);
            }
        );

        $this->loop->run();
    }
}
