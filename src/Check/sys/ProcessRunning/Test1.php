<?php

/*
 * This file is part of the Sonata Project package.
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Test\Check\sys\ProcessRunning;

use Tvi\MonitorBundle\Test\Check\CheckTestCase;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Test1 extends CheckTestCase
{
    public function testCheck()
    {
        $this->iterateConfTest(__DIR__.'/config.yml');
    }
}
