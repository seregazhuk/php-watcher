<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Filesystem;

use React\ChildProcess\Process;
use React\EventLoop\LoopInterface;
use seregazhuk\PhpWatcher\Config\WatchList;

final class ChangesListener
{
    private const WATCHER_SCRIPT = '/watcher.php';

    private $loop;

    private $watchList;

    public function __construct(LoopInterface $loop, WatchList $watchList)
    {
        $this->loop = $loop;
        $this->watchList = $watchList;
    }

    public function start(callable $onChange): void
    {
        $watcherProcess = new Process($this->scriptToRun());
        $watcherProcess->start($this->loop);

        $watcherProcess->stdout->on('data', static function ($data) use ($onChange) {
            if ((bool)$data) {
                $onChange();
            }
        });
    }

    private function scriptToRun(): string
    {
        return sprintf('php %s "%s"', __DIR__ . self::WATCHER_SCRIPT, addslashes($this->watchList->toJson()));
    }
}
