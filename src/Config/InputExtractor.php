<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Config;

use Symfony\Component\Console\Input\InputInterface;

final class InputExtractor
{
    public function __construct(private readonly InputInterface $input) {}

    public function getStringArgument(string $key, ?string $default = null): ?string
    {
        $argument = $this->input->getArgument($key);

        return $this->stringValueOrDefault($argument, $default);
    }

    public function getStringOption(string $key, ?string $default = null): ?string
    {
        $option = $this->input->getOption($key);

        return $this->stringValueOrDefault($option, $default);
    }

    private function stringValueOrDefault(mixed $value, ?string $default = null): ?string
    {
        if ($value === null) {
            return $default;
        }

        if (is_array($value) && isset($value[0])) {
            return (string) $value[0];
        }

        return (string) $value;
    }

    /**
     * @return string[]
     */
    public function getArrayOption(string $key): array
    {
        $option = $this->input->getOption($key);

        if (is_string($option) && ($option !== '' && $option !== '0')) {
            return explode(',', $option);
        }

        if (! is_array($option)) {
            return [];
        }

        return $option;
    }

    public function getFloatOption(string $key): float
    {
        return (float) $this->input->getOption($key);
    }

    public function getBooleanOption(string $key): bool
    {
        return (bool) $this->input->getOption($key);
    }
}
