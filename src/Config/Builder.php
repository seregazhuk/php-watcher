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
        $valuesFromFile = $this->valuesFromConfigFile();
        $commandLineValues = $this->valuesFromCommandLineArgs($input);
        $configValues = array_merge($valuesFromFile, $commandLineValues);

        return new Config(
            new ScriptToRun(
                $configValues['script'],
                $configValues['executable'],
                $configValues['delay'],
                $configValues['arguments']
            ),
            new WatchList(
                $configValues['watch'],
                $configValues['extensions'],
                $configValues['ignore']
            )
        );
    }

    private function valuesFromConfigFile(): array
    {
        $configFilePath = $this->findConfigFile();
        if ($configFilePath === null) {
            return [];
        }
        $values = Yaml::parse(file_get_contents($configFilePath));
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
            'extensions' => explode(',', $input->getOption('ext')),
            'ignore' => $input->getOption('ignore'),
            'delay' => (float)$input->getOption('delay'),
            'arguments' => $input->getOption('arguments'),
        ];
    }
}
