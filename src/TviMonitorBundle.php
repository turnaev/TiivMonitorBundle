<?php

namespace Tvi\MonitorBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tvi\MonitorBundle\DependencyInjection\Compiler\AddChecksCompilerPass;

class TviMonitorBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddChecksCompilerPass());
    }
}
