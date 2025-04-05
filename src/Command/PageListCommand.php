<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\JsonHubClient;
use OpenAPI\Client\Model\EntityJsonldEntityReadEntityReadParent;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'page:list',
    description: 'Add a short description for your command',
)]
class PageListCommand extends Command
{
    public function __construct(
        private JsonHubClient $jsonHubClient,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('uuid', InputArgument::REQUIRED, 'definition uuid')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $definitionUuid = $input->getArgument('uuid');

        $list = $this->jsonHubClient->getPages($definitionUuid);

        $io->table(['entity uuid', 'slug', 'def slug'], array_map(
            static fn (EntityJsonldEntityReadEntityReadParent $item): array
                => [$item->getId(), $item->getSlug(), $item->getDefinition()->getSlug()],
            $list,
        ));

        return Command::SUCCESS;
    }
}
