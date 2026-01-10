<?php

declare(strict_types=1);

namespace App\EconumoToolsBundle\Command;

use RuntimeException;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;

class MakeAPIMaker extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'make:api';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConf): void
    {
        $command
            ->setDescription('Generate base structure for API')
            ->addArgument('method', InputArgument::REQUIRED, 'HTTP method. Only GET and POST allowed')
            ->addArgument('url', InputArgument::REQUIRED, '/api/v1/account/receive-payment')
            ->addOption(
                'base-path',
                null,
                InputOption::VALUE_REQUIRED,
                'Base path to generated files',
                realpath(__DIR__ . '/../../'),
            )
            ->addOption(
                'bundle',
                null,
                InputOption::VALUE_REQUIRED,
                'Base path to generated files',
                'EconumoBundle',
            )
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'Display files which will be created',
            )
            ->addOption('delete', null, InputOption::VALUE_NONE, 'Remove generated files')
            ->setHelp(<<<HELP
API naming:
We use GET-method for methods which only read data
POST-methods - only for changing data (inserting, updating, deleting)

Base structure of URL:
/api/{VERSION}/{MODULE}/{ACTION}-{SUBJECT}

Examples:
Get accounts:
bin/console make:api GET /api/v1/account/get-account-list

Update account data:
bin/console make:api POST /api/v1/account/update-account
HELP);
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $url = strtolower(trim((string) $input->getArgument('url')));
        $httpMethod = strtoupper(trim((string) $input->getArgument('method')));
        $bundle = (string) $input->getOption('bundle');
        $basePath = (string) $input->getOption('base-path');
        $dryRun = (bool) $input->getOption('dry-run');
        $delete = (bool) $input->getOption('delete');
        $bundlePath = $basePath . '/' . $bundle;

        if (!in_array($httpMethod, ['GET', 'POST'], true)) {
            throw new RuntimeException('Wrong HTTP method');
        }

        if (
            !preg_match(
                '#^/api/(?P<version>v\d+)/(?P<module>[a-z0-9\-]+)/(?P<action>[a-z0-9]+)-(?P<subject>[a-z0-9\-]+)$#i',
                $url,
                $matches,
            )
        ) {
            throw new RuntimeException('Wrong URL');
        }

        $module = implode(
            '',
            array_map(static fn($item): string => ucfirst(strtolower((string) $item)), explode('-', $matches['module'])),
        );
        $subject = implode('', array_map(static fn(string $item): string => ucfirst(strtolower($item)), explode('-', $matches['subject'])));
        $action = ucfirst(strtolower($matches['action']));
        $version = strtoupper($matches['version']);

        $data = [
            '_CG_URL_' => $url,
            '_CG_METHOD_' => $httpMethod,
            '_CG_APPROOT_' => 'App\\' . $bundle,
            '_CG_TESTROOT_' => 'App',
        ];
        foreach (
            [
                '_CG_MODULE_' => $module,
                '_CG_SUBJECT_' => $subject,
                '_CG_ACTION_' => $action,
                '_CG_VERSION_' => $version,
            ]
            as $key => $value
        ) {
            $data[$key . 'LCFIRST_'] = lcfirst($value);
            $data[$key . 'LOWER_'] = lcfirst($value);
            $data[$key] = $value;
        }

        $templates = [
            sprintf('Controller%s.php', $httpMethod) => sprintf(
                '%s/UI/Controller/Api/%s/%s/%s%s%sController.php',
                $bundlePath,
                $module,
                $subject,
                $action,
                $subject,
                $version,
            ),
            'Form.php' => sprintf(
                '%s/UI/Controller/Api/%s/%s/Validation/%s%s%sForm.php',
                $bundlePath,
                $module,
                $subject,
                $action,
                $subject,
                $version,
            ),
            'ApiCest.php' => sprintf(
                '%s/../tests/api/%s/%s/%s%sCest.php',
                $basePath,
                strtolower($version),
                strtolower($module),
                $action,
                $subject,
            ),
            'FunctionalCest.php' => sprintf(
                '%s/../tests/functional/api/%s/%s/%s%sCest.php',
                $basePath,
                strtolower($version),
                strtolower($module),
                $action,
                $subject,
            ),
            'RequestDto.php' => sprintf(
                '%s/Application/%s/Dto/%s%s%sRequestDto.php',
                $bundlePath,
                $module,
                $action,
                $subject,
                $version,
            ),
            'ResultDto.php' => sprintf(
                '%s/Application/%s/Dto/%s%s%sResultDto.php',
                $bundlePath,
                $module,
                $action,
                $subject,
                $version,
            ),
            'ResultAssembler.php' => sprintf(
                '%s/Application/%s/Assembler/%s%s%sResultAssembler.php',
                $bundlePath,
                $module,
                $action,
                $subject,
                $version,
            ),
            'Service.php' => sprintf('%s/Application/%s/%sService.php', $bundlePath, $module, $subject),
        ];

        $filesystem = new Filesystem();
        foreach ($templates as $template => $path) {
            $io->writeln($path);
            if ($dryRun) {
                continue;
            }

            if ($delete) {
                try {
                    $filesystem->remove($path);
                } catch (Throwable $throwable) {
                    $io->write($throwable->getMessage());
                }

                continue;
            }

            clearstatcache();
            $content = $this->getContent($template, $data);
            $dir = dirname($path);
            if (!$filesystem->exists($dir)) {
                $filesystem->mkdir($dir);
            }

            if ($filesystem->exists($path)) {
                $filesystem->remove($path);
            }

            $filesystem->touch($path);
            $filesystem->appendToFile($path, $content);
        }
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        $dependencies->addClassDependency(Command::class, 'console');
    }

    private function getContent(string $file, array $data): string
    {
        $path = __DIR__ . '/../../../config/code-templates/Api/' . $file;
        $content = file_get_contents($path);
        return str_replace(array_keys($data), array_values($data), $content);
    }
}
