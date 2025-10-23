<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Filesystem;

use Evenement\EventEmitter;
use React\EventLoop\LoopInterface;
use seregazhuk\PhpWatcher\Config\WatchList;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

final class ChangesListener extends EventEmitter
{
    private const INTERVAL = 0.15;

    public function __construct(private readonly LoopInterface $loop) {}

    public function start(WatchList $watchList): void
    {
        $command = [
            (new ExecutableFinder)->find('node'),
            realpath(__DIR__.'/../../bin/file-watcher.js'),
            json_encode($watchList->getPaths()),
            json_encode($watchList->getIgnored()),
            json_encode($watchList->getFileExtensions()),
        ];

        $process = new Process(command: $command);
        $process->start();

        $this->loop->addPeriodicTimer(
            self::INTERVAL,
            function () use ($process): void {
                $output = $process->getIncrementalOutput();
                if ($output !== '') {
                    $this->emit('change');
                }
            }
        );
    }

    public function onChange(callable $callback): void
    {
        $this->on('change', $callback);
    }
}
