<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Test\Base;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
abstract class WebTestCase extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{
    public static function getKernelClass()
    {
        require_once __DIR__.'/../../app/AppKernel.php';

        return 'AppKernel';
    }

    public static function createClient(array $options = ['environment' => Kernel::MAJOR_VERSION, 'debug' => false], array $server = []): Client
    {
        static::bootKernel($options);

        return static::$kernel->getContainer()->get('test.client');
    }
}
