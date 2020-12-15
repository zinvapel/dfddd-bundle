<?php
declare(strict_types=1);

namespace Zinvapel\Basis\OpenApiAssertionBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;
use Zinvapel\Basis\BasisBundle\Core\ServiceInterface;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Dto\ProtoClassDto;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Dto\Service\GenerateDto;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Dto\Stateful\GenerationDto;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Generate\Enumeration\Target;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Transform\Assert\Dto\Stateful as Assert;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Transform\ClassString\Dto\Stateful as ClassString;
use Zinvapel\Basis\OpenApiAssertionBundle\Proto\Transform\Dto\Service\TransformDto;

final class ParseSwaggerCommand extends Command
{
    private Parser $yamlParser;
    private ServiceInterface $generateService;
    private ServiceInterface $transformAssetService;
    private ServiceInterface $transformClassService;

    public function __construct(
        Parser $yamlParser,
        ServiceInterface $generateService,
        ServiceInterface $transformAssetService,
        ServiceInterface $transformClassService
    ) {
        parent::__construct();

        $this->yamlParser = $yamlParser;
        $this->generateService = $generateService;
        $this->transformAssetService = $transformAssetService;
        $this->transformClassService = $transformClassService;
    }

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('zinvapel:oa:parse-swagger')
            ->addUsage(<<<TXT
Usage:
$ php bin/console <swagger.yaml> [--target <target> [--class <className>]]
Where:
<swagger.yaml> - path to yaml file with swagger spec
<target> - one of 'full', 'object', 'http'
<className> - for target 'object'. Generate just this class
TXT
            )
            ->addArgument('file', InputArgument::REQUIRED)
            ->addOption('target', 't', InputOption::VALUE_REQUIRED, '', Target::FULL)
            ->addOption('class', 'c', InputOption::VALUE_REQUIRED, '')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $target = Target::create($input->getOption('target'));
        } catch (\InvalidArgumentException $e) {
            $output->writeln("Unknown type '".$input->getOption('target')."'");

            return self::FAILURE;
        }

        $schema = $this->yamlParser->parseFile($input->getArgument('file'));

        $generation =
            $this->generateService->perform(
                (new GenerateDto())
                    ->setSchema($schema)
                    ->setTarget($target)
                    ->setObjectName($target->eq(Target::object()) ? $input->getOption('class') : null)
            );

        if (!$generation->getState()->isSuccess()) {
            $output->writeln('Generation failed');

            return self::FAILURE;
        }
        /* @var GenerationDto $generation */

        try {
            if ($target->eq(Target::http())) {
                foreach ($generation->getProtoMaps() as $direction => $protoMaps) {
                    $output->writeln($direction.":");
                    $this->transformProtoMaps($protoMaps, $output);
                }
            } else {
                $this->transformProtoMaps($generation->getProtoMaps(), $output);
            }
        } catch (\Throwable $t) {
            $output->writeln($t->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * @param ProtoClassDto[] $protoMaps
     * @param OutputInterface $output
     */
    private function transformProtoMaps(array $protoMaps, OutputInterface $output): void
    {
        $transformation =
            $this->transformAssetService->perform(
                (new TransformDto())
                    ->setProtoClasses($protoMaps)
            );

        if (!$transformation->getState()->isSuccess()) {
            throw new \Exception('Unable to transform to assert');
        }
        /* @var Assert\TransformationDto $transformation */

        foreach ($transformation->getAssertion() as $name => $assert) {
            $output->writeln($name . ':');
            $output->writeln($assert);
        }

        $transformation =
            $this->transformClassService->perform(
                (new TransformDto())
                    ->setProtoClasses($protoMaps)
            );

        if (!$transformation->getState()->isSuccess()) {
            throw new \Exception('Unable to transform to class');
        }
        /* @var ClassString\TransformationDto $transformation */

        foreach ($transformation->getClasses() as $name => $class) {
            $output->writeln($name . ':');
            $output->writeln($class);
        }
    }
}