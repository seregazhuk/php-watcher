<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Config;

final class Config
{
    private const DEFAULT_PHP_EXECUTABLE = 'php';
    private const DEFAULT_DELAY_IN_SECONDS = 0.25;
    private const DEFAULT_SIGNAL = SIGINT;

    private $script;

    private $phpExecutable;

    private $signal;

    private $delay;

    /**
     * @var string[]
     */
    private $arguments;

    private $watchList;

    public function __construct(string $script, ?string $phpExecutable, ?int $signal, ?float $delay, array $arguments, WatchList $watchList)
    {
        $this->script = $script;
        $this->phpExecutable = $phpExecutable ?: self::DEFAULT_PHP_EXECUTABLE;
        $this->signal = $signal ?: self::DEFAULT_SIGNAL;
        $this->delay = $delay ?: self::DEFAULT_DELAY_IN_SECONDS;
        $this->arguments = $arguments;
        $this->watchList = $watchList;
    }

    public function watchList(): WatchList
    {
        return $this->watchList;
    }

    public function command(): string
    {
        return 'exec ' . implode(' ', [$this->phpExecutable, $this->script, implode(' ', $this->arguments)]);
    }

    public function delay(): float
    {
        return $this->delay;
    }

    public function signal(): int
    {
        return $this->signal;
    }
}
