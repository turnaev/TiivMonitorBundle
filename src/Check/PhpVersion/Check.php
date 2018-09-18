<?php

namespace Tvi\MonitorBundle\Check\PhpVersion;

use Tvi\MonitorBundle\Check\CheckInterface;
use Tvi\MonitorBundle\Check\CheckTrait;

class Check extends \ZendDiagnostics\Check\PhpVersion implements CheckInterface
{
    use CheckTrait;
}
