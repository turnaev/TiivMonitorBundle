<?php

namespace Tvi\MonitorBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AddChecksCompilerPass implements CompilerPassInterface
{
    const SERVICE_ID_PREFIX_FORMAT = 'tvi_monitor.check.%s';

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->processChecks($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function processChecks(ContainerBuilder $container)
    {
        $checkConfigs = $container->getParameter('tvi_monitor.checks');
        $container->setParameter('tvi_monitor.checks', null);


        $checksMap = [];
        foreach ($checkConfigs as $checkName => $checkConfig) {

            $serviceId = sprintf(self::SERVICE_ID_PREFIX_FORMAT, $checkName);

            $checksMap[$serviceId] = ['group'=>$checkConfig['group'], 'tags'=>$checkConfig['tags']];
            $checkDefinition = $container->getDefinition($serviceId);

            foreach ($checkDefinition->getArguments() as $argumentIndex => $argument) {

                $argument = str_replace('%%', '', $argument);
                if (isset($checkConfig[$argument])) {
                    $checkDefinition->replaceArgument($argumentIndex, $checkConfig[$argument]);
                    unset($checkConfig[$argument]);
                }
            }

            $methodCalls = $checkDefinition->getMethodCalls();

            foreach ($methodCalls as &$methodCall) {
                if ($methodCall[0] == 'setAdditionParams') {
                    $methodCall[1][0] = $checkConfig;
                }
            }

            $checkDefinition->setMethodCalls($methodCalls);
        }

        $runnerDefinition = $container->getDefinition('tvi_monitor.checks.registry');

        $runnerDefinition->addMethodCall('setMap', [$container->getParameter('tvi_monitor.tags'), $checksMap]);
    }
}
