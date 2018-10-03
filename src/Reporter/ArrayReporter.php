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

use Tvi\MonitorBundle\Check\CheckInterface;
use ZendDiagnostics\Result\Collection as ResultsCollection;
use ZendDiagnostics\Result\ResultInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>, Vladimir Turnaev <turnaev@gmail.com>
 */
class ArrayReporter extends AbstractReporter
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

        $res = array_filter($res, static function ($v) {
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


    public function getCheckResults(): array
    {
        return $this->checkResults;
    }

    /**
     * {@inheritdoc}
     */
    public function onFinish(ResultsCollection $results)
    {
        parent::onFinish($results)

        $this->statusName = self::getStatusNameByCode($this->statusCode);
    }
}
