<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Config;

final class Config
{
    private const DEFAULT_PHP_EXECUTABLE = 'php';
    private const DEFAULT_DELAY_IN_SECONDS = 0.25;

    private $delay;

    private $script;

    private $phpExecutable;

    /**
     * @var string[]
     */
    private $arguments;

    private $watchList;

    public function __construct(string $script, ?string $phpExecutable, ?float $delay, array $arguments, WatchList $watchList)
    {
        $this->script = $script;
        $this->delay = $delay ?: self::DEFAULT_DELAY_IN_SECONDS;
        $this->phpExecutable = $phpExecutable ?: self::DEFAULT_PHP_EXECUTABLE;
        $this->arguments = $arguments;
        $this->watchList = $watchList;
    }

    public function watchList(): WatchList
    {
        return $this->watchList;
    }

    public function command(): string
    {
        return implode(' ', [$this->phpExecutable, $this->script, implode(' ', $this->arguments)]);
    }

    public function delay(): float
    {
        return $this->delay;
    }
}
