<?php

namespace Tvi\MonitorBundle\Check\PhpExtension;

use ZendDiagnostics\Check\CheckCollectionInterface;
use Tvi\MonitorBundle\Check\CheckCollectionTrait;
use Tvi\MonitorBundle\Check\CheckTrait;

class CheckCollection implements CheckCollectionInterface
{
    use CheckTrait;
    use CheckCollectionTrait;

    public function __construct(array $items)
    {
        foreach ($items as $name => $conf) {
//            $check = new Check($version, $comparisonOperator);
//            $check->setLabel(sprintf('PHP extension "%s" "%s"', $comparisonOperator, $version));
//
//            $this->checks[sprintf('php.extension.%s', $version)] = $check;
        }
    }
}
