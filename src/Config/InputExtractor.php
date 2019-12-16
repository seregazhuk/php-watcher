<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Config;

use Symfony\Component\Console\Input\InputInterface;

final class InputExtractor
{
    private $input;

    public function __construct(InputInterface $input)
    {
        $this->input = $input;
    }

    public function getStringArgument(string $key, string $default = null): ?string
    {
        $argument = $this->input->getArgument($key);

        return $this->stringValueOrDefault($argument, $default);
    }

    public function getStringOption(string $key, string $default = null): ?string
    {
        $option = $this->input->getOption($key);

        return $this->stringValueOrDefault($option, $default);
    }

    private function stringValueOrDefault($value, string $default = null): ?string
    {
        if ($value === null) {
            return $default;
        }

        if (is_array($value) && isset($value[0])) {
            return (string)$value[0];
        }

        return (string)$value;
    }

    public function getArrayOption(string $key): array
    {
        $option = $this->input->getOption($key);

        if (is_string($option) && !empty($option)) {
            return explode(',', $option);
        }

        if (!is_array($option)) {
            return [];
        }

        return empty($option) ? [] : $option;
    }

    public function getFloatOption(string $key): float
    {
        return (float)$this->input->getOption($key);
    }

    public function getBooleanOption(string $key): bool
    {
        return (bool)$this->input->getOption($key);
    }
}
