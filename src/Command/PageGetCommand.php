<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\JsonHubClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Uid\Uuid;

#[AsCommand(
    name: 'page:get',
    description: 'Add a short description for your command',
)]
class PageGetCommand extends Command
{
    public function __construct(
        private JsonHubClient $jsonHubClient,
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
        $entityUuid = $input->getArgument('uuid');

        $result = $this->jsonHubClient->getPageData(Uuid::fromString($entityUuid));
        $io->writeln(json_encode($result, JSON_THROW_ON_ERROR));

        return Command::SUCCESS;
    }
}
