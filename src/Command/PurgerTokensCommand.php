<?php

namespace App\Command;

use App\Repository\AccessTokenRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:tokens:purger', description: 'Supprime les tokens expirés')]
class PurgerTokensCommand extends Command
{
    public function __construct(
        private AccessTokenRepository $tokenRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->tokenRepository->supprimerTokensExpires();
        $output->writeln('Tokens expirés supprimés');

        return Command::SUCCESS;
    }
}