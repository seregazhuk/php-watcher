<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Filesystem\ChangesListener;

use Evenement\EventEmitter;
use React\EventLoop\LoopInterface;
use React\EventLoop\TimerInterface;
use seregazhuk\PhpWatcher\Config\WatchList;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

final class ChokidarChangesListener extends EventEmitter implements ChangesListenerInterface
{
    private const INTERVAL = 0.15;

    private ?Process $process = null;

    private ?TimerInterface $timer = null;

    public function __construct(private readonly LoopInterface $loop) {}

    public function start(WatchList $watchList): void
    {
        $command = [
            (new ExecutableFinder)->find('node'),
            realpath(__DIR__.'/../../../bin/file-watcher.js'),
            json_encode($watchList->getPaths()),
            json_encode($watchList->getIgnored()),
            json_encode($watchList->getFileExtensions()),
        ];

        $this->process = new Process(command: $command);
        $this->process->start();

        $this->timer = $this->loop->addPeriodicTimer(
            self::INTERVAL,
            function (): void {
                $output = $this->process->getIncrementalOutput();
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

    public function stop(): void
    {
        if ($this->process instanceof Process && $this->process->isRunning()) {
            $this->process->stop();
        }

        if ($this->timer instanceof TimerInterface) {
            $this->loop->cancelTimer($this->timer);
        }
    }

    public function getName(): string
    {
        return 'chokidar';
    }
}
