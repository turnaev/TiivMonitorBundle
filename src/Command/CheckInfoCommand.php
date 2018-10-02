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
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
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
            ->setDescription('Info Health Checkers')
            ->addOption(
                'name',
                'i',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Groups filter'
            )
            ->addOption(
                'groups',
                'g',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Groups filter'
            )
            ->addOption(
                'tags',
                't',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Tags filter'
            )
            ->setHelp(
                <<<'EOT'
The <info>%command.name%</info> get check info

* INfo Checks:

  <info>php %command.full_name% [--alias=... ,] [--groups=... ,] [--tags==... ,] </info>

EOT
            );
    }

    /**
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $namesFilter = $input->getOption('name');
        $namesFilter = ($namesFilter) ? $namesFilter : null;

        $groupsFilter = $input->getOption('groups');
        $groupsFilter = ($groupsFilter) ? $groupsFilter : null;

        $tagsFilter = $input->getOption('tags');
        $tagsFilter = ($tagsFilter) ? $tagsFilter : null;

        $manager = $this->manager;
        $checks = $manager->findChecks($namesFilter, $groupsFilter, $tagsFilter);

        $table = new Table($output);
        $table->setHeaders(['Group', 'Tag(s)', 'Check', 'Label']);

        $groupOld = null;
        foreach ($checks as $check) {

            $tags = $check->getTags();
            if ($tags) {
                $tags = $manager->findTags($check->getTags());
                $tags = array_map(function($t) {
                    return $t->getLabel();
                }, $tags);

                $tags = implode(', ', $tags);
            } else {
                $tags = null;
            }

            $group = null;
            $groupNew = sprintf('<fg=yellow;options=bold>%-8s</>', $check->getGroup());
            if($groupOld != $groupNew) {
                if($groupOld) {
                    $table->addRow(new TableSeparator());
                }
                $group = $groupOld = $groupNew;
            }
            $checkAlias = sprintf('<info>%s</info>', $check->getId());
            $table->addRow([$group, $tags, $checkAlias, $check->getLabel()]);
        }

        $table->render();
    }
}
