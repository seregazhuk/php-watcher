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

    public function fromConfigFile(string $path = null): Config
    {
        $pathToConfig = empty($path) ? $this->findConfigFile() : $path;
        $values = empty($pathToConfig) ? [] : $this->valuesFromConfigFile($pathToConfig);

        return Config::fromArray($values);
    }

    public function fromCommandLineArgs(InputInterface $input): Config
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
}
