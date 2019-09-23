<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Config;

final class ScriptToRun
{
    /**
     * @var string
     */
    public $command;

    /**
     * @var float
     */
    public $delay;

    public function __construct(string $script, string $phpExecutable, float $delay, array $arguments)
    {
        $this->delay = $delay;
        $this->command = array_merge([$phpExecutable], explode(' ', $script), $arguments);
    }
}
