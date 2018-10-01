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

use ZendDiagnostics\Result\FailureInterface;
use ZendDiagnostics\Result\ResultInterface;
use ZendDiagnostics\Result\SkipInterface;
use ZendDiagnostics\Result\SuccessInterface;
use ZendDiagnostics\Result\WarningInterface;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
abstract class AbstractReporter
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
     * @return string
     */
    public static function getStatusNameByCode(int $statusCode): string
    {
        return isset(self::$STATUS_MAP[$statusCode]) ? self::$STATUS_MAP[$statusCode] : self::STATUS_NAME_UNKNOWN;
    }

    /**
     * @param ResultInterface $result
     *
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
