<?php

namespace Tvi\MonitorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class CheckGeneratorCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tvi:monitor:generator:check')
            ->setAliases(['generate:monitor:check'])
            ->setDescription('Generates check plugin from tvi monitor template')
            ->addArgument('name', InputArgument::REQUIRED, 'A bundle name, a namespace, or a class name')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command generates check classes
from tvi monitor template:

* Create a check:
  
  <info>php %command.full_name% "Tvi\MonitorBundle\Check\Example"</info>

EOT
            );
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

        $name = $input->getArgument('name');
        v($name);


//        try {
//            $bundle = $this->getApplication()->getKernel()->getBundle();
//            v($bundle);
//        } catch (\InvalidArgumentException $e) {
//            $name = strtr($input->getArgument('name'), '/', '\\');
//            v($name);
////            $pos  = strpos($name, ':');
////
////            v($pos);
//
//
//
//        }



    }
}
