<?php

namespace Tvi\MonitorBundle\Check\Example;

use Tvi\MonitorBundle\Test\Base\CheckTestCase;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Test extends CheckTestCase
{
    public function testCheck()
    {
        $this->iterateConfTest(__DIR__.'/config.example.yml');
    }
}
