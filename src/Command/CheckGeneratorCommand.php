<?php

namespace Tvi\MonitorBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class CheckGeneratorCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('tvi:monitor:generator:check')
            ->setDescription('Runs Health Checks')
            ->addArgument(
                'checkName',
                InputArgument::OPTIONAL,
                'The name of the service to be used to perform the health check.'
            )
            ->addOption(
                'reporter',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Additional reporters to run.'
            )
            ->addOption('nagios', null, InputOption::VALUE_NONE, 'Suitable for using as a nagios NRPE command.')
            ->addOption(
                'group',
                'g',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Run Health Checks for given group'
            )
            ->addOption('all', 'a', InputOption::VALUE_NONE, 'Run Health Checks of all groups')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        v(1);
    }
}
