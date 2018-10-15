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
use Tvi\MonitorBundle\Runner\RunnerManager;
use Tvi\MonitorBundle\Reporter\ReporterManager;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class CheckHealthCommand extends Command
{
    /**
     * @var RunnerManager
     */
    private $runnerManager;

    /**
     * @var ReporterManager
     */
    private $reporterManager;

    /**
     * @param ?string $name
     */
    public function __construct(ReporterManager $reporterRunnerManager, RunnerManager $runnerManager, string $name = null)
    {
        $this->reporterManager = $reporterRunnerManager;
        $this->runnerManager = $runnerManager;

        parent::__construct($name);
    }

    protected function configure()
    {
        $reporterAliases = $this->reporterManager->getReporterAliases('console');
        $reporterAliases = implode(', ', $reporterAliases);
        $this
            ->setName(
                'tvi:monitor:check:run'
            )
            ->setDescription(
                'Runs health checks'
            )
            ->addOption(
                'check',
                'c',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Check filter'
            )
            ->addOption(
                'reporter',
                'r',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                "Additional reporters to run. Use reporter(s) [{$reporterAliases}].",
                ['console']
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
            ->addOption(
                'break-on-failure',
                'b',
                InputOption::VALUE_NONE,
                'Break checking on any check failure.'
            )
            ->setHelp(
                <<<'EOT'
The <info>%command.name%</info> get check info

* INfo Checks:

  <info>php %command.full_name% [--check=... ,] [--group=... ,] [--tag==... ,]  [--break-on-failure]</info>

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
        $breakOnFailure = $input->getOption('break-on-failure', false);

        $runner = $this->runnerManager->getRunner($checkFilter, $groupFilter, $tagFilter);
        $runner->setBreakOnFailure($breakOnFailure);

        $reporters = $input->getOption('reporter');
        foreach ($reporters as $reporterAlias) {
            $reporter = $this->reporterManager->getReporter($reporterAlias);
            if ($reporter) {
                $runner->addReporter($reporter);
            } else {
                $output->writeln(sprintf('Reporter <info>"%s"</info> not found, skip it.', $reporterAlias));
            }
        }

        $runner->run();
    }
}
