<?php

declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Config;

use Symfony\Component\Yaml\Yaml;

final class Builder
{
    private const SUPPORTED_CONFIG_NAMES = [
        '.php-watcher.yml',
        'php-watcher.yml',
        'php-watcher.yml.dist',
    ];

    public function fromConfigFile(?string $path = null): Config
    {
        $pathToConfig = $path === null || $path === '' || $path === '0' ? $this->findConfigFile() : $path;
        $values = $pathToConfig === null || $pathToConfig === '' || $pathToConfig === '0' ? [] : $this->valuesFromConfigFile($pathToConfig);

        return Config::fromArray($values);
    }

    public function fromCommandLineArgs(InputExtractor $input): Config
    {
        $values = $this->valuesFromCommandLineArgs($input);

        return Config::fromArray($values);
    }

    /**
     * @return array{
     *      script: string|null,
     *      executable: string|null,
     *      signal: int|null,
     *      delay: float|null,
     *      arguments: string[],
     *      no-spinner: bool,
     *      watch: string[],
     *      extensions: string[],
     *      ignore: string[]
     *  }
     */
    private function valuesFromConfigFile(string $configFilePath): array
    {
        $contents = file_get_contents($configFilePath);
        if ($contents === false) {
            throw InvalidConfigFileContents::invalidContents($configFilePath);
        }

        $values = Yaml::parse($contents);
        if (! is_array($values)) {
            throw InvalidConfigFileContents::invalidContents($configFilePath);
        }

        return $values;
    }

    public function findConfigFile(): ?string
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

    /**
     * @return array{
     *      script: string|null,
     *      executable: string|null,
     *      watch: string[],
     *      extensions: string[],
     *      ignore: string[],
     *      signal: int|null,
     *      delay: float|null,
     *      arguments: string[],
     *      no-spinner: bool,
     *  }
     */
    private function valuesFromCommandLineArgs(InputExtractor $input): array
    {
        return [
            'script' => $input->getStringArgument('script'),
            'executable' => $input->getStringOption('exec'),
            'watch' => $input->getArrayOption('watch'),
            'extensions' => $input->getArrayOption('ext'),
            'ignore' => $input->getArrayOption('ignore'),
            'signal' => $input->getStringOption('signal'),
            'delay' => $input->getFloatOption('delay'),
            'arguments' => $input->getArrayOption('arguments'),
            'no-spinner' => $input->getBooleanOption('no-spinner'),
        ];
    }
}
