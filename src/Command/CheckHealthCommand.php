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
use Tvi\MonitorBundle\Reporter\ConsoleReporter;
use Tvi\MonitorBundle\Reporter\RawConsoleReporter;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class CheckHealthCommand extends Command
{
    /**
     * @var Manager
     */
    private $manager;

    /**
     * @param ?string $name
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
            ->setDescription('Runs health checks')
            ->addOption(
                'reporter',
                'r',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Additional reporters to run.',
                ['sss']
            )
            ->addOption(
                'check',
                'c',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Check filter'
            )
            ->addOption(
                'group',
                'g',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Groups filter'
            )
            ->addOption(
                'tag',
                't',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Tag(s) filter'
            )
            ->setHelp(
                <<<'EOT'
The <info>%command.name%</info> get check info

* INfo Checks:

  <info>php %command.full_name% [--check=... ,] [--group=... ,] [--tag==... ,] </info>

EOT
            );
    }

    /**
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $checkFilter = $input->getOption('check');
        $groupFilter = $input->getOption('group');
        $tagFilter = $input->getOption('tag');

        $runner = $this->manager->getRunner($checkFilter, $groupFilter, $tagFilter);

        $reporter = new ConsoleReporter($output);
        $runner->addReporter($reporter);

//        $reporter = new RawConsoleReporter($output);
//        $runner->addReporter($reporter);

        $runner->run();
    }
}
