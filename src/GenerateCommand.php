<?php

namespace Kimeo;

use DateTime;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('kimeo:generate')
            ->setDescription('A simple CLI tool to generate GitHub activity report.')
            ->addArgument('from', InputArgument::REQUIRED, 'Starting date in Y-m-d format')
            ->addArgument('to', InputArgument::REQUIRED, 'Ending date in Y-m-d format')
            ->addArgument('branches', InputArgument::REQUIRED|InputArgument::IS_ARRAY, 'The filtered git branches')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $user = getenv('GITHUB_LOGIN');
        $password = getenv('GITHUB_PASSWORD');
        $owner = getenv('GITHUB_OWNER');
        $project = getenv('GITHUB_REPOSITORY');

        $from = new DateTime($input->getArgument('from'));
        $to = new DateTime($input->getArgument('to'));
        $branches = $input->getArgument('branches');

        $generator = new GitHubGenerator($user, $password, $owner, $project);
        $generator->generate($from, $to, $branches);
        
        (new Filesystem())->dumpFile('report.md', $generator->getReport());
        $io->success('File `report.md` generated without any errors.');
    }
}
