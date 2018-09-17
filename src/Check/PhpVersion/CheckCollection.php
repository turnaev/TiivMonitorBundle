<?php

namespace MonitorBundle\Check\PhpVersion;

use ZendDiagnostics\Check\CheckCollectionInterface;
use MonitorBundle\Check\CheckCollectionTrait;
use MonitorBundle\Check\CheckTrait;

class CheckCollection implements CheckCollectionInterface
{
    use CheckTrait;
    use CheckCollectionTrait;

    public function __construct(array $items)
    {
        foreach ($items as $name => $conf) {

            list($expectedVersion, $operator) = [$conf['expectedVersion'], $conf['operator']];

            $check = new Check($expectedVersion, $operator);
            $check->setLabel(sprintf('PHP version "%s" "%s"', $expectedVersion, $operator));

            $this->checks[$name] = $check;
        }
    }
}
