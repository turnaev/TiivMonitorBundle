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
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Tvi\MonitorBundle\Reporter\Console;
use Tvi\MonitorBundle\Runner\RunnerManager;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class CheckInfoCommand extends Command
{
    /**
     * @var Manager
     */
    private $runnerManager;

    /**
     * @param ?string $name
     */
    public function __construct(RunnerManager $runnerManager, string $name = null)
    {
        parent::__construct($name);
        $this->runnerManager = $runnerManager;
    }

    protected function configure()
    {
        $this
            ->setName('tvi:monitor:check:info')
            ->setDescription('Info health checks')
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

        $checks = $this->runnerManager->findChecksSorted($checkFilter, $groupFilter, $tagFilter);

        $table = new Table($output);
        $table->setHeaders(['Check', 'Tag(s)', 'Label']);

        $groupOld = null;
        foreach ($checks as $check) {
            $tags = $check->getTags();

            if ($tags) {
                $tags = $this->runnerManager->findTags($check->getTags());
                $tags = array_map(static function ($t) {
                    return $t->getLabel();
                }, $tags);

                $tags = implode(', ', $tags);
            } else {
                $tags = null;
            }

            $group = null;
            $groupNew = sprintf('<fg=default;options=bold>%s</>', $check->getGroup());

            if ($groupOld !== $groupNew) {
                if ($groupOld) {
                    $table->addRow(new TableSeparator());
                }
                $table->addRow([new TableCell($groupNew, array('colspan' => 3))]);
                $table->addRow(new TableSeparator());

                $group = $groupOld = $groupNew;
            }

            $importanceTag = Console::tagByImportance($check->getImportance());
            $id = sprintf('%s%s</>', $importanceTag, $check->getId());
            $table->addRow([$id, $tags, $check->getLabel()]);
        }

        $table->render();
    }
}
