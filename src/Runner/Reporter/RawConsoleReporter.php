<?php

namespace Tvi\MonitorBundle\Runner\Reporter;

use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use ZendDiagnostics\Check\CheckInterface;
use ZendDiagnostics\Result\ResultInterface;
use ZendDiagnostics\Result\SkipInterface;
use ZendDiagnostics\Result\SuccessInterface;
use ZendDiagnostics\Result\WarningInterface;
use ZendDiagnostics\Runner\Reporter\ReporterInterface;
use ZendDiagnostics\Result\Collection as ResultsCollection;

/**
 * @author Kevin Bond <kevinbond@gmail.com>, Vladimir Turnaev <turnaev@gmail.com>
 */
class RawConsoleReporter extends AbstractReporter implements ReporterInterface
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
                $statusOut = 'OK';
                break;

            case $result instanceof WarningInterface:
                $statusOut = 'WARNING';
                break;

            case $result instanceof SkipInterface:
                $statusOut = 'SKIP';
                break;

            default:
                $statusOut = 'FAIL';
        }

        $this->output->writeln(sprintf('%-7s %s', $statusOut, $check->getLabel()));
    }

    /**
     * {@inheritdoc}
     */
    public function onStart(\ArrayObject $checks, $runnerConfig)
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function onBeforeRun(CheckInterface $check, $checkAlias = null)
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function onStop(ResultsCollection $results)
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function onFinish(ResultsCollection $results)
    {
        return;
    }
}
