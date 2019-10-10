<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Config;

final class Config
{
    private const DEFAULT_PHP_EXECUTABLE = 'php';
    private const DEFAULT_DELAY_IN_SECONDS = 0.25;
    private const UNWRAP_COMMAND = 'exec';

    private $delay;

    private $script;

    private $phpExecutable;

    private $unwrap;

    /**
     * @var string[]
     */
    private $arguments;

    private $watchList;

    public function __construct(string $script, ?string $phpExecutable, bool $unwrap, ?float $delay, array $arguments, WatchList $watchList)
    {
        $this->script = $script;
        $this->delay = $delay ?: self::DEFAULT_DELAY_IN_SECONDS;
        $this->phpExecutable = $phpExecutable ?: self::DEFAULT_PHP_EXECUTABLE;
        $this->arguments = $arguments;
        $this->watchList = $watchList;
        $this->unwrap = $unwrap;
    }

    public function watchList(): WatchList
    {
        return $this->watchList;
    }

    public function command(): string
    {
    	$commandComponents = [$this->phpExecutable, $this->script, implode(' ', $this->arguments)];
    	if ($this->unwrap) {
    		array_unshift($commandComponents, self::UNWRAP_COMMAND);
	    }
        return implode(' ', $commandComponents);
    }

    public function delay(): float
    {
        return $this->delay;
    }
}
