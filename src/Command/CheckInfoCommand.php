<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tvi\MonitorBundle\Runner\Manager;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class CheckInfoCommand extends Command
{
    /**
     * @var Manager
     */
    private $manager;

    /**
     * @param Manager $manager
     * @param null    $name
     */
    public function __construct(Manager $manager, string $name = null)
    {
        parent::__construct($name);
        $this->manager = $manager;
    }

    protected function configure()
    {
        $this
            ->setName('tvi:monitor:check:info')
            ->setDescription('Info Health Checkers')
            ->addOption('group', 'g', InputOption::VALUE_OPTIONAL, 'Check group')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $runner = $this->manager->getRunner();
        $runner->run();
        print_r($runner);
//        switch (true) {
//            case $input->getOption('reporters'):
//                $this->listReporters($input, $output);
//                break;
//            case $input->getOption('groups'):
//                $this->listGroups($input, $output);
//                break;
//            default:
//                $this->listChecks($input, $output);
//                break;
//        }
    }

    protected function listChecks(InputInterface $input, OutputInterface $output)
    {
        $group = $input->getOption('group');

        $checkMetadatas = $this->manager->getCheckMetadatas($group);

        if (0 === \count($checkMetadatas)) {
            if (empty($group)) {
                $output->writeln(sprintf('<error>No checks configured.</error>'));
            } else {
                $output->writeln(sprintf('<error>No checks configured for group %s.</error>', $group));
            }
        }

        $showGroup = null;
        foreach ($checkMetadatas as $checkMetadata) {
            $currentGroup = $checkMetadata->getGroup();
            if (empty($group) && $showGroup != $currentGroup) {
                $output->writeln(sprintf('<fg=yellow;options=bold>%s</>', $currentGroup));
                $showGroup = $currentGroup;
            }

            $check = $checkMetadata->getCheck();
            $alias = $checkMetadata->getAlias();
            $output->writeln(sprintf('<info>%s</info> %s', $alias, $check->getLabel()));
        }
    }

    /**
     * @param OutputInterface $output
     */
    protected function listReporters(InputInterface $input, OutputInterface $output)
    {
        $group = $input->getOption('group');
        $runner = $this->manager->getRunner($group);

        if (null === $runner) {
            $output->writeln('<error>No such group.</error>');

            return;
        }

        $reporters = $runner->getAdditionalReporters();
        if (0 === \count($reporters)) {
            $output->writeln('<error>No additional reporters configured.</error>');
        }
        foreach (array_keys($reporters) as $reporter) {
            $output->writeln($reporter);
        }
    }

    /**
     * @param OutputInterface $output
     */
    protected function listGroups(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->manager->getGroups() as $group) {
            $output->writeln(sprintf('<fg=yellow;options=bold>%s</>', $group));
        }
    }
}
