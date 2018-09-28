<?php
/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\sys\CpuPerformance;

use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\Success;
use ZendDiagnostics\Result\Warning;
use Tvi\MonitorBundle\Check\CheckInterface;
use Tvi\MonitorBundle\Check\CheckTrait;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Check extends \ZendDiagnostics\Check\CpuPerformance implements CheckInterface
{
    use CheckTrait;

    /**
     * @inheritdoc
     */
    public function check()
    {
        // Check if bcmath extension is present
        // @codeCoverageIgnoreStart
        if (! extension_loaded('bcmath')) {
            return new Warning('Check\CpuPerformance requires BCMath extension to be loaded.');
        }
        // @codeCoverageIgnoreEnd

        $timeStart = microtime(true);
        $result = static::calcPi(1000);
        $duration = microtime(true) - $timeStart;
        $performance = round($duration / $this->baseline, 5);

        if ($result != $this->expectedResult) {
            // Ignore code coverage here because it's impractical to test against faulty calculations.
            // @codeCoverageIgnoreStart
            return new Warning('PI calculation failed. This might mean CPU or RAM failure', $result);
            // @codeCoverageIgnoreEnd
        } elseif ($performance > $this->minPerformance) {
            return new Success(sprintf('Cpu Performance is %.5f.', $performance), $performance);
        } else {
            return new Failure(null, $performance);
        }
    }
}
