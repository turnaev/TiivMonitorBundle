<?php

namespace MonitorBundle\Test\Base;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpKernel\Kernel;

abstract class WebTestCase extends BaseWebTestCase
{
    static public function getKernelClass()
    {
        require_once __DIR__.'/../app/AppKernel.php';

        return 'AppKernel';
    }

    static function createClient(array $options = ['environment'=>Kernel::MAJOR_VERSION, 'debug'=>true], array $server = []): Client
    {
        static::bootKernel($options);

        return static::$kernel->getContainer()->get('test.client');
    }
}
