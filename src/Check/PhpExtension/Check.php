<?php

namespace MonitorBundle\Check\PhpExtension;

use MonitorBundle\Check\CheckTrait;

class Check extends \ZendDiagnostics\Check\ExtensionLoaded
{
    use CheckTrait;
}
