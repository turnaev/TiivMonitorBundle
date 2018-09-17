<?php

namespace Tvi\MonitorBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use ZendDiagnostics\Check\CheckCollectionInterface;

class AddChecksCompilerPass implements CompilerPassInterface
{
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
        $checkConfigs = $container->getParameter('tvi_monitor.checks.conf');

        $container->setParameter('tvi_monitor.checks.conf', null);

        $checkServiceIds = $container->findTaggedServiceIds('tvi_monitor.check');
        $checksMap = [];
        foreach ($checkServiceIds as $checkServiceId => $tags) {

            $checkDefinition = $container->getDefinition($checkServiceId);

            $checkServiceAlias = $tags[0]['alias'];
            $checkConfig = $checkConfigs[$checkServiceAlias];

            $checksMap[$checkServiceId] = ['id'=>$checkServiceAlias, 'group'=>$checkConfig['group'], 'tags'=>$checkConfig['tags']];

            if(in_array(CheckCollectionInterface::class, class_implements($checkDefinition->getClass()))) {

                foreach ($checkConfig['items']  as &$items) {

                    if(empty($items['tags'])) {
                        $items['tags'] = $checkConfig['tags'];
                    }
                }

                $checksMap[$checkServiceId]['items'] = $checkConfig['items'];
            }

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

                    $checkConfig['id'] = $checkServiceAlias;
                    $methodCall[1][0] = $checkConfig;
                }
            }
            $checkDefinition->setMethodCalls($methodCalls);
        }

        //exit;
        $runnerDefinition = $container->getDefinition('tvi_monitor.checks.registry');
        $runnerDefinition->addMethodCall('init', [$container->getParameter('tvi_monitor.tags'), $checksMap]);
    }
}
