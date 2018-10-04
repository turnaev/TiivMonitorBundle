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
use ZendDiagnostics\Result\Collection as ResultsCollection;
use ZendDiagnostics\Result\ResultInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>, Vladimir Turnaev <turnaev@gmail.com>
 */
class Console extends ReporterAbstract
{
    private const STATUS_TAG_SUCCESS = '<fg=green;options=bold>';
    private const STATUS_TAG_WARNING = '<fg=yellow;options=bold>';
    private const STATUS_TAG_SKIP = '<fg=blue;options=bold>';
    private const STATUS_TAG_FAILUR = '<fg=red;options=bold>';
    private const STATUS_TAG_UNKNOWN = '<fg=white;options=bold>';
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var bool
     */
    protected $withData;

    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output = null, $withData = false)
    {
        if (null === $output) {
            $output = new ConsoleOutput();
        }
        $this->output = $output;

        $this->withData = $withData;
    }

    /**
     * {@inheritdoc}
     */
    public function onAfterRun(CheckInterface $check, ResultInterface $result, $checkAlias = null)
    {
        list($status, $code) = $this->getStatusByResul($result);

        $tag = $this->tagByCode($code);

        $groupTag = $check->getGroup();
        $tags = $check->getTags();

        if ($tags) {
            $tags = implode(', ', $tags);
            $tags = "[$tags]";
            $groupTag .= ' / '.$tags;
        } else {
            $tags = null;
        }

        $message = $result->getMessage();
        if ($message) {
            $message = ", {$message}";
        }
        $this->output->writeln(sprintf('<info>%-30s</info> %-40s %s%-10s</> %s%s',
            $check->getId(), $groupTag, $tag, $status, $check->getLabel(), $message));

        if ($this->withData) {
            $dataOut = $this->getDataOut($result);
            if ($dataOut) {
                $this->output->writeln(sprintf('%-71s < %s >', ' ', $dataOut));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function onStart(\ArrayObject $checks, $runnerConfig)
    {
        parent::onStart($checks, $runnerConfig);

        $this->output->writeln(sprintf('<info>%-30s %-40s %-10s %s, %s</info>', 'Check', 'Group / Tag(s)', 'Status', 'Info', 'Message'));
    }

    /**
     * {@inheritdoc}
     */
    public function onFinish(ResultsCollection $results)
    {
        parent::onFinish($results);

        $this->output->writeln('---------------------------------');
        $this->output->writeln(sprintf('%s: %s', 'total', $this->getTotalCount()));
        $this->output->writeln('');
        $this->output->writeln(sprintf('%s%-10s</>: %s', self::STATUS_TAG_SUCCESS, 'SUCCESSES', $this->getSuccessCount()));
        $this->output->writeln(sprintf('%s%-10s</>: %s', self::STATUS_TAG_WARNING, 'WARNINGS', $this->getWarningCount()));
        $this->output->writeln(sprintf('%s%-10s</>: %s', self::STATUS_TAG_SKIP, 'SKIP', $this->getSkipCount()));
        $this->output->writeln(sprintf('%s%-10s</>: %s', self::STATUS_TAG_FAILUR, 'FAILURES', $this->getFailureCount()));
        $this->output->writeln(sprintf('%s%-10s</>: %s', self::STATUS_TAG_UNKNOWN, 'UNKNOWNS', $this->getUnknownCount()));
    }

    protected function tagByCode($code): string
    {
        $tags = [
            self::STATUS_CODE_SUCCESS => self::STATUS_TAG_SUCCESS,
            self::STATUS_CODE_WARNING => self::STATUS_TAG_WARNING,
            self::STATUS_CODE_SKIP => self::STATUS_TAG_SKIP,
            self::STATUS_CODE_FAILURE => self::STATUS_TAG_FAILUR,
            'default' => self::STATUS_TAG_UNKNOWN,
        ];

        return $tags[$code] ?? $tags['default'];
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

                if (\mb_strlen($dataOut) > 100) {
                    $dataOut .= "\n";
                }
            }
        }

        return $dataOut;
    }
}
