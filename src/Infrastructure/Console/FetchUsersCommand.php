<?php

namespace App\Infrastructure\Console;

use App\Application\UseCases\Users\Commands\FetchUserListCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:fetch-users',
    description: 'Download user list from external API and store it in the database',
)]
class FetchUsersCommand extends Command
{
    public function __construct(
        private MessageBusInterface $messageBus
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('count', InputArgument::OPTIONAL, 'Number of users to fetch (default: 10)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $countArg = $input->getArgument('count');
        if (!empty($countArg)) {
            if (!is_numeric($countArg)) {
                $io->error('Invalid count value. Please provide a valid integer.');
                return Command::FAILURE;
            }
            $count = (int)$countArg;
        } else {
            $count = 10;
        }
        try {
            $this->messageBus->dispatch(new FetchUserListCommand($count));
        } catch (\Exception $e) {
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
