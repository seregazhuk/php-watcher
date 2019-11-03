<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Config;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Yaml\Yaml;

final class Builder
{
    private const SUPPORTED_CONFIG_NAMES = [
        '.php-watcher.yml',
        'php-watcher.yml',
        'php-watcher.yml.dist',
    ];

    public function build(InputInterface $input): Config
    {
        $valuesFromFile = $this->getValuesFromConfigFile($input);
        $commandLineValues = $this->valuesFromCommandLineArgs($input);
        $configValues = $this->mergeConfigValues($valuesFromFile, $commandLineValues);

        return new Config(
            $configValues['script'],
            $configValues['executable'],
            $configValues['signal'],
            $configValues['delay'],
            $configValues['arguments'],
            $configValues['no-spinner'],
            new WatchList(
                $configValues['watch'],
                $configValues['extensions'],
                $configValues['ignore']
            )
        );
    }

    private function valuesFromConfigFile(string $configFilePath): array
    {
        $contents = file_get_contents($configFilePath);
        if ($contents === false) {
            throw InvalidConfigFileContents::invalidContents($configFilePath);
        }

        $values = Yaml::parse($contents);
        if ($values === null) {
            throw InvalidConfigFileContents::invalidContents($configFilePath);
        }

        return $values;
    }

    private function findConfigFile(): ?string
    {
        $configDirectory = getcwd();
        foreach (self::SUPPORTED_CONFIG_NAMES as $configName) {
            $configFullPath = "{$configDirectory}/{$configName}";

            if (file_exists($configFullPath)) {
                return $configFullPath;
            }
        }

        return null;
    }

    private function valuesFromCommandLineArgs(InputInterface $input): array
    {
        return [
            'script' => $input->getArgument('script'),
            'executable' => $input->getOption('exec'),
            'watch' => $input->getOption('watch'),
            'extensions' => empty($input->getOption('ext')) ? [] : explode(',', $input->getOption('ext')),
            'ignore' => $input->getOption('ignore'),
            'signal' => $input->getOption('signal') ? constant($input->getOption('signal')) : null,
            'delay' => (float)$input->getOption('delay'),
            'arguments' => $input->getOption('arguments'),
            'no-spinner' => $input->getOption('no-spinner') !== false,
        ];
    }

    private function getConfigPath(InputInterface $input): ?string
    {
        $pathFromCommandLine = (string)$input->getOption('config');

        return empty($pathFromCommandLine) ? $this->findConfigFile() : $pathFromCommandLine;
    }

    private function getValuesFromConfigFile(InputInterface $input): array
    {
        $configFilePath = $this->getConfigPath($input);
        if ($configFilePath === null) {
            $valuesFromFile = [];
        } else {
            $valuesFromFile = $this->valuesFromConfigFile($configFilePath);
        }
        return $valuesFromFile;
    }

    private function mergeConfigValues(array $valuesFromFile, array $commandLineValues): array
    {
        $configValues = [];
        foreach ($commandLineValues as $key => $value) {
            if (empty($value) && isset($valuesFromFile[$key])) {
                $configValues[$key] = $valuesFromFile[$key];
            } else {
                $configValues[$key] = $commandLineValues[$key];
            }
        }

        return $configValues;
    }
}
