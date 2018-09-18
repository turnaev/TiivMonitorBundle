<?php

namespace Tvi\MonitorBundle\Check\PhpExtension;

use Tvi\MonitorBundle\Check\CheckInterface;
use Tvi\MonitorBundle\Check\CheckTrait;

class Check extends \ZendDiagnostics\Check\ExtensionLoaded implements CheckInterface
{
    use CheckTrait;
}
