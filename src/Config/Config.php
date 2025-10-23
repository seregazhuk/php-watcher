<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Config;

final class Config
{
    private const DEFAULT_DELAY_IN_SECONDS = 0.25;

    private const DEFAULT_SIGNAL_TO_RELOAD = SIGINT;

    private readonly string $phpExecutable;

    private readonly int $signalToReload;

    private readonly float $delay;

    public function __construct(private readonly ?string $script, ?string $phpExecutable, ?int $signalToReload, ?float $delay, /**
     * @var string[]
     */
        private readonly array $arguments, private readonly bool $spinnerDisabled, private readonly \seregazhuk\PhpWatcher\Config\WatchList $watchList)
    {
        $this->phpExecutable = $phpExecutable !== null && $phpExecutable !== '' && $phpExecutable !== '0' ? $phpExecutable : PHP_BINARY;
        $this->signalToReload = $signalToReload !== null && $signalToReload !== 0 ? $signalToReload : self::DEFAULT_SIGNAL_TO_RELOAD;
        $this->delay = $delay ?: self::DEFAULT_DELAY_IN_SECONDS;
    }

    /**
     * @param array{
     *     script: string|null,
     *     executable: string|null,
     *     signal: string|null,
     *     delay: float|null,
     *     arguments: string[]|null,
     *     no-spinner: bool|null,
     *     watch: string[]|null,
     *     extensions: string[]|null,
     *     ignore: string[]|null,
     * } $values
     */
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
            return 'exec '.$commandline;
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
            ($this->script === null || $this->script === '' || $this->script === '0') && $another->script ? $another->script : $this->script,
            $this->phpExecutable === PHP_BINARY && $another->phpExecutable ? $another->phpExecutable : $this->phpExecutable,
            $this->signalToReload === self::DEFAULT_SIGNAL_TO_RELOAD && $another->signalToReload ? $another->signalToReload : $this->signalToReload,
            $this->delay === self::DEFAULT_DELAY_IN_SECONDS && $another->delay ? $another->delay : $this->delay,
            $this->arguments === [] && $another->arguments !== [] ? $another->arguments : $this->arguments,
            $another->spinnerDisabled ?: $this->spinnerDisabled,
            $another->watchList->merge($this->watchList)
        );
    }
}
