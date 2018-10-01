<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Runner\Reporter;

use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use ZendDiagnostics\Check\CheckInterface;
use ZendDiagnostics\Result\Collection as ResultsCollection;
use ZendDiagnostics\Result\FailureInterface;
use ZendDiagnostics\Result\ResultInterface;
use ZendDiagnostics\Result\SkipInterface;
use ZendDiagnostics\Result\SuccessInterface;
use ZendDiagnostics\Result\WarningInterface;
use ZendDiagnostics\Runner\Reporter\ReporterInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>, Vladimir Turnaev <turnaev@gmail.com>
 */
class ConsoleReporter extends AbstractReporter implements ReporterInterface
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
        switch (true) {
            case $result instanceof SuccessInterface:
                $statusOut = sprintf('<info>%s</info>', self::STATUS_NAME_SUCCESS);
                break;

            case $result instanceof WarningInterface:
                $statusOut = sprintf('<comment>%s</comment>', self::STATUS_NAME_WARNING);
                break;

            case $result instanceof SkipInterface:
                $statusOut = sprintf('<question>%s</question>', self::STATUS_NAME_SKIP);
                break;

            case $result instanceof FailureInterface:
                $statusOut = sprintf('<error>%s</error>', self::STATUS_NAME_FAILURE);
                break;

            default:
                $statusOut = sprintf('<question>%s</question>', self::STATUS_NAME_UNKNOWN);
        }

        $this->output->writeln(sprintf('%-50s %s %s', $check->getLabel(), $statusOut, $result->getMessage()));

        //$dataOut = $this->getDataOut($result);
        //if($dataOut) {
        //    $this->output->writeln(sprintf('%s', $dataOut));
        //}
    }

    /**
     * {@inheritdoc}
     */
    public function onStart(\ArrayObject $checks, $runnerConfig)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function onBeforeRun(CheckInterface $check, $checkAlias = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function onStop(ResultsCollection $results)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function onFinish(ResultsCollection $results)
    {
        $this->output->writeln('');
    }

    /**
     * @return null|string
     */
    protected function getDataOut(ResultInterface $result)
    {
        $dataOut = null;
        $message = $result->getMessage();
        if ($message) {
            $data = $result->getData();
            if (null !== $data) {
                $dataOut = json_encode($data);

                if (\strlen($dataOut) > 100) {
                    $dataOut .= "\n";
                }
            }
        }

        return $dataOut;
    }
}
