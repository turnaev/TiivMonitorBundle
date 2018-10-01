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
class GroupInfoCommand extends Command
{
    /**
     * @var Manager
     */
    private $manager;

    /**
     * @param null $name
     */
    public function __construct(Manager $manager, string $name = null)
    {
        parent::__construct($name);
        $this->manager = $manager;
    }

    protected function configure()
    {
        $this
            ->setName('tvi:monitor:group:info')
            ->setDescription('Info Groups')
            ->addOption(
                'groups',
                'g',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Groups filter'
            )
            ->setHelp(
                <<<'EOT'
The <info>%command.name%</info> get check info

* Info Groups:

  <info>php %command.full_name% [--groups=... ,] </info>

EOT
            )
        ;
    }

    /**
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $groupsFilter = $input->getOption('groups');
        $groupsFilter = ($groupsFilter) ? $groupsFilter : null;

        $manager = $this->manager;
        $groups = $manager->findGroups($groupsFilter);

        foreach ($groups as $tag) {
            $output->writeln(sprintf('<fg=yellow;options=bold>%s</>', $tag->getLabel()));
            foreach ($tag as $check) {
                $output->writeln(sprintf('<info>%-40s</info> %s', $check->getId(), $check->getLabel()));
            }
            $output->writeln('');
        }
    }
}
