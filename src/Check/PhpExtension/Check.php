<?php

namespace Tvi\MonitorBundle\Check\PhpExtension;

use Tvi\MonitorBundle\Check\CheckTrait;

class Check extends \ZendDiagnostics\Check\ExtensionLoaded
{
    use CheckTrait;
}
