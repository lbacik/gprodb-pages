<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\LandingPageService;
use GProDB\LandingPage\Elements\AbstractElement;
use GProDB\LandingPage\LandingPage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Uid\Uuid;

#[AsCommand(
    name: 'page:structure',
    description: 'Print the page structure',
)]
class PageStructureCommand extends Command
{
    public function __construct(
        private LandingPageService $landingPageService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('uuid', InputArgument::REQUIRED, 'page entity uuid')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $pageEntity = $input->getArgument('uuid');

        /** @var LandingPage $page */
        $page = $this->landingPageService->get(Uuid::fromString($pageEntity));

        /** @var AbstractElement $element */
        foreach ($page->getElements() as $element) {
            $io->writeln('element: ' . $element->elementName()->name);

            dump($element->toArray());
        }

        return Command::SUCCESS;
    }
}
