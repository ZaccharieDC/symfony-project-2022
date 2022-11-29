<?php

namespace App\Command;

use App\Repository\AdvertRepository;
use DateInterval;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'advert:delete-published',
    description: 'Allows you to delete adverts that have been published for X amount of time',
)]
class DeletePublishedAdvertCommand extends Command
{
    private AdvertRepository $advertRepository;

    public function __construct(AdvertRepository $advertRepository)
    {
        $this->advertRepository = $advertRepository;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('days', InputArgument::REQUIRED, 'Number of days');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $days = $input->getArgument('days');

        if ($days) {
            $io->note(sprintf('You passed an argument: %s', $days));
        }

        try {
            $interval = sprintf('P%sD', $days);

            $today = new \DateTimeImmutable();
            $date = $today->sub(new DateInterval($interval));

            $this->advertRepository->removePublished($date, true);

            $io->success('All published adverts have been removed.');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Oops, a problem occurred');
            return Command::FAILURE;
        }
    }
}
