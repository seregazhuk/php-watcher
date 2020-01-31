<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher;

use Symfony\Component\Console\Application;

final class ConsoleApplication extends Application
{
    public const NAME = 'PHP-Watcher';
    public const VERSION = '0.5.2';

    public function __construct()
    {
        parent::__construct(self::NAME, self::VERSION);
        $this->add(new WatcherCommand());
    }

    public function getLongVersion(): string
    {
        return parent::getLongVersion() . ' by <comment>Sergey Zhuk</comment>';
    }
}
