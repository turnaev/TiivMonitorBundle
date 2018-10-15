<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check;

use JMS\Serializer\Annotation as JMS;

/**
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessorOrder("custom", custom = { "id", "label", "group", "descr", "importance", "tags"})
 *
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
abstract class CheckAbstract extends \ZendDiagnostics\Check\AbstractCheck implements CheckInterface
{
    use CheckTrait;

    const IMPORTANCE_EMERGENCY = 'EMERGENCY';
    const IMPORTANCE_WARNING = 'WARNING';
    const IMPORTANCE_NOTE = 'NOTE';
    const IMPORTANCE_INFO = 'INFO';

    /**
     * @return string[]
     */
    public static function getImportances(): array
    {
        return [
            self::IMPORTANCE_EMERGENCY => self::IMPORTANCE_EMERGENCY,
            self::IMPORTANCE_WARNING => self::IMPORTANCE_WARNING,
            self::IMPORTANCE_NOTE => self::IMPORTANCE_NOTE,
            self::IMPORTANCE_INFO => self::IMPORTANCE_INFO,
        ];
    }
}
