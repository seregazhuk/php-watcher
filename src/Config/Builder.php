<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Config;

use Symfony\Component\Yaml\Yaml;

final class Builder
{
    private const SUPPORTED_CONFIG_NAMES = [
        '.php-watcher.yml',
        'php-watcher.yml',
        'php-watcher.yml.dist',
    ];

    public function fromConfigFile(string $path = null): Config
    {
        $pathToConfig = empty($path) ? $this->findConfigFile() : $path;
        $values = empty($pathToConfig) ? [] : $this->valuesFromConfigFile($pathToConfig);

        return Config::fromArray($values);
    }

    public function fromCommandLineArgs(InputExtractor $input): Config
    {
        $values = $this->valuesFromCommandLineArgs($input);
        return Config::fromArray($values);
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
