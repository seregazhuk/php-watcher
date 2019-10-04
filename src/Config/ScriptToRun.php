<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Config;

final class ScriptToRun
{
    private const DEFAULT_PHP_EXECUTABLE = 'php';
    private const DEFAULT_DELAY_IN_SECONDS = 1;

    /**
     * @var float
     */
    private $delay;

    /**
     * @var string
     */
    private $script;

    /**
     * @var string|null
     */
    private $phpExecutable;

    /**
     * @var array
     */
    private $arguments;

    public function __construct(string $script, ?string $phpExecutable, ?float $delay, array $arguments)
    {
        $this->script = $script;
        $this->delay = $delay ?: self::DEFAULT_DELAY_IN_SECONDS;
        $this->phpExecutable = $phpExecutable ?: self::DEFAULT_PHP_EXECUTABLE;
        $this->arguments = $arguments;
    }

    public function command(): array
    {
        return array_merge([$this->phpExecutable], explode(' ', $this->script), $this->    arguments);
    }

    public function delay(): float
    {
        return $this->delay;
    }
}
