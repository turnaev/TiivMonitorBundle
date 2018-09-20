<?php

namespace Tvi\MonitorBundle\Test\Base;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\Yaml\Yaml;
use Tvi\MonitorBundle\DependencyInjection\Compiler\AddChecksCompilerPass;
use Tvi\MonitorBundle\DependencyInjection\TviMonitorExtension;

abstract class ExtensionTestCase extends AbstractExtensionTestCase
{
    protected function getContainerExtensions()
    {
        return [new TviMonitorExtension()];
    }

    protected function compile()
    {
        $this->container->addCompilerPass(new AddChecksCompilerPass());

        parent::compile();
    }

    /**
     * @param string $fileName
     *
     * @return array
     */
    protected function parceYaml($fileName)
    {
        return Yaml::parseFile($fileName);
    }
}
