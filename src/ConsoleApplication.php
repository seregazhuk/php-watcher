<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher;

use Symfony\Component\Console\Application;

final class ConsoleApplication extends Application
{
    public function __construct()
    {
        parent::__construct('PHP-Watcher', '0.3.0');
        $this->add(new WatcherCommand());
    }

    public function getLongVersion(): string
    {
        return parent::getLongVersion() . ' by <comment>Sergey Zhuk</comment>';
    }
}
