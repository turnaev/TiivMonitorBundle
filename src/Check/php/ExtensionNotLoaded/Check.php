<?php

/*
 * This file is part of the Sonata Project package.
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\php\ExtensionNotLoaded;

use Tvi\MonitorBundle\Check\CheckInterface;
use Tvi\MonitorBundle\Check\CheckTrait;

use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\Success;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Check extends \ZendDiagnostics\Check\ExtensionLoaded implements CheckInterface
{
    use CheckTrait;

    /**
     * @return Failure|\ZendDiagnostics\Result\ResultInterface|Success
     */
    public function check()
    {
        $r = parent::check();
        if ($r instanceof Success) {
            return new Failure($r->getMessage(), $r->getData());
        }

        return new Success($r->getMessage(), $r->getData());
    }
}
