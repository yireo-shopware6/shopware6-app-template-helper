<?php declare(strict_types=1);

namespace Yireo\AppTemplate\Command;

use Shopware\Core\Framework\App\Lifecycle\Persister\TemplatePersister;
use Shopware\Core\Framework\App\Manifest\Manifest;
use Shopware\Core\Framework\App\Template\TemplateStateService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshTemplatesCommand extends Command
{
    protected static $defaultName = 'yireo:app-template:refresh';

    private TemplateStateService $templateStateService;
    private EntityRepositoryInterface $entityRepository;
    private TemplatePersister $templatePersister;
    private string $projectDir;

    public function __construct(
        EntityRepositoryInterface $entityRepository,
        TemplatePersister $templatePersister,
        TemplateStateService $templateStateService,
        string $projectDir,
        string $name = null
    ) {
        parent::__construct($name);
        $this->entityRepository = $entityRepository;
        $this->templatePersister = $templatePersister;
        $this->templateStateService = $templateStateService;
        $this->projectDir = $projectDir;
    }

    protected function configure()
    {
        $this->setDescription('Refresh app templates of specified app');
        $this->addArgument('name', InputArgument::OPTIONAL, 'App name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $context = Context::createDefaultContext();
        $appName = $input->getArgument('name');

        $criteria = new Criteria();
        if ($appName) {
            $criteria->addFilter(new EqualsFilter('name', $appName));
        }

        $apps = $this->entityRepository->search($criteria, $context);

        if (count($apps) < 0) {
            $output->writeln('<error>No app(s) found</error>');
            return 1;
        }

        foreach ($apps as $app) {
            $appName = $app->name;
            $manifest = Manifest::createFromXmlFile($this->projectDir . '/custom/apps/' . $appName . '/manifest.xml');
            $this->templatePersister->updateTemplates($manifest, $app->id, $context);
            $this->templateStateService->activateAppTemplates($app->id, $context);
        }

        return 1;
    }
}
