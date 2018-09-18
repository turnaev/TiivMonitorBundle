<?php

namespace Tvi\MonitorBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Tvi\MonitorBundle\DependencyInjection\Tags;
use ZendDiagnostics\Check\CheckCollectionInterface;

class AddChecksCompilerPass implements CompilerPassInterface
{
    const SERVICE_ID_FORMAT = 'tvi_monitor.check.%s';

    /**
     * @var array
     */
    protected $checkServiceMap = [];

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->processChecks($container);
    }

    protected function addCheckService(ContainerBuilder $container, Definition $checkDefinitionTpl, array $conf, string $checkServiceAlias, string $name = null)
    {
        $checkDefinition = clone $checkDefinitionTpl;

        if ($name) {
            $checkServiceAlias .= '.' . $name;
        }

        foreach ($checkDefinition->getArguments() as $argumentIndex => $argument) {

            $argument = str_replace('%%', '', $argument);

            if (isset($conf['check'][$argument])) {
                $checkDefinition->replaceArgument($argumentIndex, $conf['check'][$argument]);
            }
        }

        $methodCalls = $checkDefinition->getMethodCalls();

        foreach ($methodCalls as &$methodCall) {
            if ($methodCall[0] == 'setAdditionParams') {

                $conf['id'] = $checkServiceAlias;
                $methodCall[1][0] = $conf;
            }
        }

        $checkDefinition->setMethodCalls($methodCalls);

        $checkServiceId = sprintf(self::SERVICE_ID_FORMAT, $checkServiceAlias);
        $container->setDefinition($checkServiceId, $checkDefinition);

        $this->checkServiceMap[$checkServiceAlias] = ['serviceId'=>$checkServiceId, 'group'=>$conf['group'], 'tags'=>$conf['tags']];
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function processChecks(ContainerBuilder $container)
    {
        $checkConfigs = $container->getParameter('tvi_monitor.checks.conf');
        $container->setParameter('tvi_monitor.checks.conf', null);

        $checkServiceIds = $container->findTaggedServiceIds(Tags::CHECK);

        foreach ($checkServiceIds as $checkServiceId => $tags) {

            $checkDefinitionTpl = $container->getDefinition($checkServiceId);

            $container->removeDefinition($checkServiceId);

            $checkTag = $checkDefinitionTpl->getTag(Tags::CHECK);
            $checkServiceAlias = $checkTag[0]['alias'];

            $checkConfig = $checkConfigs[$checkServiceAlias];

            if (isset($checkConfig['_singl'])) {
                $this->addCheckService($container, $checkDefinitionTpl, $checkConfig['_singl'], $checkServiceAlias);
            }

            if (isset($checkConfig['_multi'])) {
                foreach ($checkConfig['_multi'] as $name => $conf) {
                    $this->addCheckService($container, $checkDefinitionTpl, $conf, $checkServiceAlias, $name);
                }
            }
        }

        $registryDefinition = $container->getDefinition('tvi_monitor.checks.registry');
        $registryDefinition->addMethodCall('init', [$container->getParameter('tvi_monitor.tags'), $this->checkServiceMap]);
    }
}
