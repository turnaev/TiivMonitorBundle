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
use ArrayObject;
use ZendDiagnostics\Check\CheckInterface;
use ZendDiagnostics\Result\Collection as ResultsCollection;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
abstract class AbstractReporter implements ReporterInterface
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

    public static function getStatusNameByCode(int $statusCode): string
    {
        return isset(self::$STATUS_MAP[$statusCode]) ? self::$STATUS_MAP[$statusCode] : self::STATUS_NAME_UNKNOWN;
    }

    /**
     * This method is called right after Reporter starts running, via Runner::run().
     *
     * @param ArrayObject $checks       A collection of Checks that will be performed
     * @param array       $runnerConfig Complete Runner configuration, obtained via Runner::getConfig()
     */
    public function onStart(ArrayObject $checks, $runnerConfig)
    {
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
    }

    /**
     * This method is called when Runner has finished its run.
     *
     * @param ResultsCollection $results collection of Results for performed Checks
     */
    public function onFinish(ResultsCollection $results)
    {
    }

    /**
     * @return array [name, code]
     */
    protected function getStatusByResul(ResultInterface $result)
    {
        switch (true) {
            case $result instanceof SuccessInterface:
                $name = self::STATUS_NAME_SUCCESS;
                $code = self::STATUS_CODE_SUCCESS;
                break;

            case $result instanceof WarningInterface:
                $name = self::STATUS_NAME_WARNING;
                $code = self::STATUS_CODE_WARNING;
                break;

            case $result instanceof SkipInterface:
                $name = self::STATUS_NAME_SKIP;
                $code = self::STATUS_CODE_SKIP;
                break;

            case $result instanceof FailureInterface:
                $name = self::STATUS_NAME_FAILURE;
                $code = self::STATUS_CODE_FAILURE;
                break;

            default:
                $name = self::STATUS_NAME_UNKNOWN;
                $code = self::STATUS_CODE_UNKNOWN;
                break;
        }

        return [$name, $code];
    }
}
