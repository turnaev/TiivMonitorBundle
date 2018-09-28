<?php
/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Test\Check\fs\JsonFile;

use Tvi\MonitorBundle\Test\Check\CheckTestCase;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Test extends CheckTestCase
{
    public function testCheck()
    {
        $this->iterateConfTest(__DIR__.'/config.yml');
    }
}
