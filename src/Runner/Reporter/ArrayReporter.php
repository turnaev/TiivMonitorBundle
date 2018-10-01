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

use Tvi\MonitorBundle\Check\CheckInterface;
use ZendDiagnostics\Result\Collection as ResultsCollection;
use ZendDiagnostics\Result\ResultInterface;
use ZendDiagnostics\Runner\Reporter\ReporterInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>, Vladimir Turnaev <turnaev@gmail.com>
 */
class ArrayReporter extends AbstractReporter implements ReporterInterface
{
    /**
     * @var int
     */
    protected $statusCode = self::STATUS_CODE_SUCCESS;

    /**
     * @var string
     */
    protected $statusName = self::STATUS_NAME_SUCCESS;

    /**
     * @var array
     */
    protected $checkResults = [];

    /**
     * @var ResultsCollection
     */
    protected $results;

    /**
     * {@inheritdoc}
     */
    public function onAfterRun(CheckInterface $check, ResultInterface $result, $checkAlias = null)
    {
        list($statusName, $statusCode) = $this->getStatusByResul($result);

        $this->statusCode = max($this->statusCode, $statusCode);

        $res = [
            'statusCode' => $statusCode,
            'statusName' => $statusName,
            'label' => $check->getLabel(),
            'check' => $checkAlias,
            'message' => $result->getMessage(),
            'tags' => $check->getTags(),
            'group' => $check->getGroup(),
        ];

        $data = $result->getData();
        if (null !== $data) {
            if ($data instanceof \Exception) {
                $res['data'] = $data->getMessage();
            } else {
                $res['data'] = $data;
            }
        }

        $res = array_filter($res, function ($v) {
            return \is_array($v) ? !empty($v) : (null !== $v);
        });

        $this->checkResults[] = $res;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    public function getStatusName()
    {
        return $this->statusName;
    }

    /**
     * @return int
     */
    public function getSuccessCount(): ?int
    {
        return $this->results ? $this->results->getSuccessCount() : null;
    }

    /**
     * @return int
     */
    public function getWarningCount(): ?int
    {
        return $this->results ? $this->results->getWarningCount() : null;
    }

    /**
     * @return int
     */
    public function getFailureCount(): ?int
    {
        return $this->results ? $this->results->getFailureCount() : null;
    }

    /**
     * @return int
     */
    public function getSkipCount(): ?int
    {
        return $this->results ? $this->results->getSkipCount() : null;
    }

    /**
     * @return int
     */
    public function getUnknownCount(): ?int
    {
        return $this->results ? $this->results->getUnknownCount() : null;
    }

    /**
     * @return array
     */
    public function getCheckResults(): array
    {
        return $this->checkResults;
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
        $this->results = $results;

        $this->statusName = self::getStatusNameByCode($this->statusCode);
    }
}
