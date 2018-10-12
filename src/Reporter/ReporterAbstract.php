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

use ZendDiagnostics\Result\FailureInterface;
use ZendDiagnostics\Result\ResultInterface;
use ZendDiagnostics\Result\SkipInterface;
use ZendDiagnostics\Result\SuccessInterface;
use ZendDiagnostics\Result\WarningInterface;
use ZendDiagnostics\Runner\Reporter\ReporterInterface;
use ZendDiagnostics\Check\CheckInterface;
use ZendDiagnostics\Result\Collection as ResultsCollection;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
abstract class ReporterAbstract implements ReporterInterface
{
    public const STATUS_CODE_SUCCESS = 0;
    public const STATUS_CODE_WARNING = 100;
    public const STATUS_CODE_SKIP = 200;
    public const STATUS_CODE_UNKNOWN = 300;
    public const STATUS_CODE_FAILURE = 1000;

    public const STATUS_NAME_SUCCESS = 'SUCCESS';
    public const STATUS_NAME_WARNING = 'WARNING';
    public const STATUS_NAME_SKIP = 'SKIP';
    public const STATUS_NAME_UNKNOWN = 'UNKNOWN';
    public const STATUS_NAME_FAILURE = 'FAILURE';

    public static $STATUS_MAP = [
        self::STATUS_CODE_SUCCESS => self::STATUS_NAME_SUCCESS,
        self::STATUS_CODE_WARNING => self::STATUS_NAME_WARNING,
        self::STATUS_CODE_SKIP => self::STATUS_NAME_SKIP,
        self::STATUS_CODE_UNKNOWN => self::STATUS_NAME_UNKNOWN,
        self::STATUS_CODE_FAILURE => self::STATUS_NAME_FAILURE,
    ];

    /**
     * Total number of Checks.
     *
     * @var int
     */
    protected $totalCount = 0;

    /**
     * Has the Runner operation been aborted (stopped) ?
     *
     * @var bool
     */
    protected $stopped = false;

    /**
     * @var ResultsCollection
     */
    protected $results;

    public static function getStatusNameByCode(int $statusCode): string
    {
        return static::$STATUS_MAP[$statusCode] ?? static::STATUS_NAME_UNKNOWN;
    }

    /**
     * This method is called right after Reporter starts running, via Runner::run().
     *
     * @param ArrayObject $checks       A collection of Checks that will be performed
     * @param array       $runnerConfig Complete Runner configuration, obtained via Runner::getConfig()
     */
    public function onStart(\ArrayObject $checks, $runnerConfig)
    {
        $this->stopped = false;

        $this->totalCount = \count($checks);
    }

    /**
     * This method is called before each individual Check is performed. If this
     * method returns false, the Check will not be performed (will be skipped).
     *
     * @param CheckInterface $check      check instance that is about to be performed
     * @param string|null    $checkAlias The alias for the check that is about to be performed
     *
     * @return bool|void Return false to prevent check from happening
     */
    public function onBeforeRun(CheckInterface $check, $checkAlias = null)
    {
    }

    /**
     * This method is called every time a Check has been performed. If this method
     * returns false, the Runner will not perform any additional checks and stop
     * its run.
     *
     * @param CheckInterface  $check      A Check instance that has just finished running
     * @param ResultInterface $result     Result for that particular check instance
     * @param string|null     $checkAlias The alias for the check that has just finished
     *
     * @return bool|void Return false to prevent from running additional Checks
     */
    public function onAfterRun(CheckInterface $check, ResultInterface $result, $checkAlias = null)
    {
    }

    /**
     * This method is called when Runner has been aborted and could not finish the
     * whole run().
     *
     * @param ResultsCollection $results collection of Results for performed Checks
     */
    public function onStop(ResultsCollection $results)
    {
        $this->stopped = true;
    }

    /**
     * This method is called when Runner has finished its run.
     *
     * @param ResultsCollection $results collection of Results for performed Checks
     */
    public function onFinish(ResultsCollection $results)
    {
        $this->results = $results;

        // Display information that the test has been aborted.
        if ($this->stopped) {
            $this->onStopped();
        }
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

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    public function isStopped(): bool
    {
        return $this->stopped;
    }

    public function getResults(): ResultsCollection
    {
        return $this->results;
    }

    protected function onStopped()
    {
    }

    /**
     * @return array [name, code]
     */
    protected function getStatusByResul(ResultInterface $result)
    {
        switch (true) {
            case $result instanceof SuccessInterface:
                $name = static::STATUS_NAME_SUCCESS;
                $code = static::STATUS_CODE_SUCCESS;
                break;

            case $result instanceof WarningInterface:
                $name = static::STATUS_NAME_WARNING;
                $code = static::STATUS_CODE_WARNING;
                break;

            case $result instanceof SkipInterface:
                $name = static::STATUS_NAME_SKIP;
                $code = static::STATUS_CODE_SKIP;
                break;

            case $result instanceof FailureInterface:
                $name = static::STATUS_NAME_FAILURE;
                $code = static::STATUS_CODE_FAILURE;
                break;

            default:
                $name = static::STATUS_NAME_UNKNOWN;
                $code = static::STATUS_CODE_UNKNOWN;
                break;
        }

        return [$name, $code];
    }
}
