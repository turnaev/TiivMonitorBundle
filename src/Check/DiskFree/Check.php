<?php
/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\DiskFree;

use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\Success;
use ZendDiagnostics\Result\SuccessInterface;
use ZendDiagnostics\Result\Warning;
use ZendDiagnostics\Result\WarningInterface;
use ZendDiagnostics\Result\SkipInterface;
use ZendDiagnostics\Result\FailureInterface;

use Tvi\MonitorBundle\Check\CheckInterface;
use Tvi\MonitorBundle\Check\CheckTrait;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Check extends \ZendDiagnostics\Check\DiskFree implements CheckInterface
{
    use CheckTrait;

    /**
     * @inheritdoc
     */
    public function check()
    {
        // We are using error suppression because the method will trigger a warning
        // in case of non-existent paths and other errors. We are more interested in
        // the potential return value of FALSE, which will tell us that free space
        // could not be obtained and we do not care about the real cause of this.
        $free = @ disk_free_space($this->path);

        if ($free === false || ! is_float($free) || $free < 0) {
            return new Warning('Unable to determine free disk space at ' . $this->path .'.');
        }

        $freeHumanReadable = static::bytesToString($free, 2);
        $minFreeHumanReadable = static::bytesToString($this->minDiskBytes, 2);
        $description = sprintf('Remaining space at %s: %s, requared min: %s.', $this->path, $freeHumanReadable, $minFreeHumanReadable);

        if (disk_free_space($this->path) < $this->minDiskBytes) {
            return new Failure($description, $free);
        }

        return new Success($description, $free);
    }
}
