<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Watcher;

use React\ChildProcess\Process;
use React\EventLoop\LoopInterface;
use seregazhuk\PhpWatcher\Filesystem\ChangesListener;
use seregazhuk\PhpWatcher\Screen;

final class Watcher
{
    private $loop;

    private $screen;

    private $filesystemListener;

    public function __construct(LoopInterface $loop, Screen $screen, ChangesListener $filesystemListener)
    {
        $this->loop = $loop;
        $this->screen = $screen;
        $this->filesystemListener = $filesystemListener;
    }

    public function startWatching(Process $process, float $delayToRestart): void
    {
        $this->screen->start($process->getCommand());

        $this->startProcess($process);

        $this->filesystemListener->start(function () use ($process, $delayToRestart) {
            $process->terminate();
            $this->screen->restarting($process->getCommand());

            $this->loop->addTimer($delayToRestart, function () use ($process) {
                $this->startProcess($process);
            });
        });

        $this->loop->run();
    }

    private function startProcess(Process $process): void
    {
        $process->start($this->loop);
        $this->screen->subscribeToProcessOutput($process);
    }
}
