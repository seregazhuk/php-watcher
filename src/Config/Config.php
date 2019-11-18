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

    private $spinnerDisabled;

    private $watchList;

    public function __construct(?string $script, ?string $phpExecutable, ?int $signal, ?float $delay, array $arguments, bool $spinnerDisabled, WatchList $watchList)
    {
        $this->script = $script;
        $this->phpExecutable = $phpExecutable ?: self::DEFAULT_PHP_EXECUTABLE;
        $this->signal = $signal ?: self::DEFAULT_SIGNAL;
        $this->delay = $delay ?: self::DEFAULT_DELAY_IN_SECONDS;
        $this->arguments = $arguments;
        $this->spinnerDisabled = $spinnerDisabled;
        $this->watchList = $watchList;
    }

    public static function fromArray(array $values): self
    {
        return new self(
            $values['script'] ?? null,
            $values['executable'] ?? null,
            $values['signal'] ?? null,
            $values['delay'] ?? null,
            $values['arguments'] ?? [],
            $values['no-spinner'] ?? false,
            new WatchList(
                $values['watch'] ?? [],
                $values['extensions'] ?? [],
                $values['ignore'] ?? []
            )
        );
    }

    public function watchList(): WatchList
    {
        return $this->watchList;
    }

    public function command(): string
    {
        $commandline = implode(' ', [$this->phpExecutable, $this->script, implode(' ', $this->arguments)]);
        if ('\\' !== DIRECTORY_SEPARATOR) {
            // exec is mandatory to deal with sending a signal to the process
            $commandline = 'exec '.$commandline;
        }

        return $commandline;
    }

    public function delay(): float
    {
        return $this->delay;
    }

    public function signal(): int
    {
        return $this->signal;
    }

    public function spinnerDisabled(): bool
    {
        return $this->spinnerDisabled;
    }

    public function merge(self $another): self
    {
        return new self(
            empty($this->script) && $another->script ? $another->script : $this->script,
            $this->phpExecutable === self::DEFAULT_PHP_EXECUTABLE && $another->phpExecutable ? $another->phpExecutable: $this->phpExecutable,
            $this->signal === self::DEFAULT_SIGNAL && $another->signal ? $another->signal : $this->signal,
            $this->delay === self::DEFAULT_DELAY_IN_SECONDS && $another->delay ? $another->delay: $this->delay,
            empty($this->arguments) && !empty($another->arguments) ? $another->arguments : $this->arguments,
            $another->spinnerDisabled ?: $this->spinnerDisabled,
            $another->watchList->merge($this->watchList)
        );
    }
}
