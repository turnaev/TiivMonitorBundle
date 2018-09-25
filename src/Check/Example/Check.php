<?php

namespace Tvi\MonitorBundle\Check\Example;

use ZendDiagnostics\Result\Success;
use ZendDiagnostics\Result\SuccessInterface;
use ZendDiagnostics\Result\WarningInterface;
use ZendDiagnostics\Result\SkipInterface;
use ZendDiagnostics\Result\FailureInterface;

use Tvi\MonitorBundle\Check\CheckInterface;
use Tvi\MonitorBundle\Check\CheckTrait;

class Check extends \ZendDiagnostics\Check\AbstractCheck implements CheckInterface
{
    use CheckTrait;

    /**
     * @see \ZendDiagnostics\Check\CheckInterface::check()
     * @return SuccessInterface|WarningInterface|SkipInterface|FailureInterface
     */
    public function check()
    {
        return new Success();
    }
}
