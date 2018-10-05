<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Reporter;

use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use ZendDiagnostics\Check\CheckInterface;
use ZendDiagnostics\Result\ResultInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>, Vladimir Turnaev <turnaev@gmail.com>
 */
class Nagius extends ReporterAbstract
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output = null)
    {
        if (null === $output) {
            $output = new ConsoleOutput();
        }
        $this->output = $output;
    }

    /**
     * {@inheritdoc}
     */
    public function onAfterRun(CheckInterface $check, ResultInterface $result, $checkAlias = null)
    {
        list($_, $code) = $this->getStatusByResul($result);

        $statusOut = $this->statusByCode($code);
        $this->output->writeln(sprintf("%-8s\t%-25s\t%s", $statusOut, $check->getId(), $check->getLabel()));
    }

    protected function statusByCode($code): string
    {
        $tags = [
            self::STATUS_CODE_SUCCESS => 'OK',
            self::STATUS_CODE_WARNING => 'WARNING',
            self::STATUS_CODE_SKIP => 'SKIP',
            self::STATUS_CODE_FAILURE => 'FAIL',
            'default' => 'FAIL',
        ];

        return $tags[$code] ?? $tags['default'];
    }
}
