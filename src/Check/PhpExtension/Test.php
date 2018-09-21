<?php

namespace Tvi\MonitorBundle\Check\PhpExtension;

use Tvi\MonitorBundle\Test\Base\CheckTestCase;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Test extends CheckTestCase
{
    public function testCheck()
    {
        throw new \Exception();
        $this->iterateConfTest(__DIR__.'/config.example.yml');
    }
}
