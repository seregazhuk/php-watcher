<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Config;

final class Config
{
    private const DEFAULT_DELAY_IN_SECONDS = 0.25;
    private const DEFAULT_SIGNAL_TO_RELOAD = SIGINT;

    private $script;

    private $phpExecutable;

    private $signalToReload;

    private $delay;

    /**
     * @var string[]
     */
    private $arguments;

    private $spinnerDisabled;

    private $watchList;

    public function __construct(?string $script, ?string $phpExecutable, ?int $signalToReload, ?float $delay, array $arguments, bool $spinnerDisabled, WatchList $watchList)
    {
        $this->script = $script;
        $this->phpExecutable = $phpExecutable ?: PHP_BINARY;
        $this->signalToReload = $signalToReload ?: self::DEFAULT_SIGNAL_TO_RELOAD;
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
            isset($values['signal']) ? constant($values['signal']) : null,
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

    public function signalToReload(): int
    {
        return $this->signalToReload;
    }

    public function spinnerDisabled(): bool
    {
        return $this->spinnerDisabled;
    }

    public function merge(self $another): self
    {
        return new self(
            empty($this->script) && $another->script ? $another->script : $this->script,
            $this->phpExecutable === PHP_BINARY && $another->phpExecutable ? $another->phpExecutable: $this->phpExecutable,
            $this->signalToReload === self::DEFAULT_SIGNAL_TO_RELOAD && $another->signalToReload ? $another->signalToReload : $this->signalToReload,
            $this->delay === self::DEFAULT_DELAY_IN_SECONDS && $another->delay ? $another->delay: $this->delay,
            empty($this->arguments) && !empty($another->arguments) ? $another->arguments : $this->arguments,
            $another->spinnerDisabled ?: $this->spinnerDisabled,
            $another->watchList->merge($this->watchList)
        );
    }
}
