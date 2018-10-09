<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Test\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Response;
use Tvi\MonitorBundle\Runner\RunnerManager;
use Tvi\MonitorBundle\Test\Base\WebTestCase;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 *
 * @internal
 */
class ApiControllerInfoCheckTest extends WebTestCase
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Router
     */
    private $router;

    protected function setUp()
    {
        $this->client = $this->getClient(false);
        $this->router = $this->client->getContainer()->get('router');
    }

    /**
     * @dataProvider checksProvider
     */
    public function test_api_info_checks($params, $code, $count)
    {
        $req = $this->router->generate('tvi_monitor.routing.api.info.check(s)', $params, false);
        $this->client->request('GET', $req);

        $response = $this->client->getResponse();
        $content = $response->getContent();

        $this->assertSame($code, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        $this->assertJson($content);

        $content = json_decode($content);

        $this->assertCount($count, $content, sprintf('%s=%s', $count, \count($content)));
    }

    public function test_api_info_checks500()
    {
        $managerMock = $this->createMock(RunnerManager::class);
        $managerMock->method('findChecks')->willThrowException(new \Exception());
        $this->client->getContainer()->set('tvi_monitor.runner.manager', $managerMock);

        $req = $this->router->generate('tvi_monitor.routing.api.info.check(s)', [], false);
        $this->client->request('GET', $req);

        $response = $this->client->getResponse();

        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    /**
     * @dataProvider checkProvider
     */
    public function test_api_info_check($params, $code, $res)
    {
        $req = $this->router->generate('tvi_monitor.routing.api.info.check', $params, false);
        $this->client->request('GET', $req);

        $response = $this->client->getResponse();
        $content = $response->getContent();

        $this->assertSame($code, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        $this->assertJson($content);

        $content = json_decode($content);

        foreach ($res as $f => $v) {
            $this->assertSame($v, $content->{$f}, sprintf('%s=%s', print_r($f, true), print_r($v, true)));
        }
    }

    public function test_api_info_check500()
    {
        $managerMock = $this->createMock(RunnerManager::class);
        $managerMock->method('findChecks')->willThrowException(new \Exception());
        $this->client->getContainer()->set('tvi_monitor.runner.manager', $managerMock);

        $req = $this->router->generate('tvi_monitor.routing.api.info.check', ['id' => 'core:php_version'], false);
        $this->client->request('GET', $req);

        $response = $this->client->getResponse();

        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    /**
     * @return array [$params, $code, $res]
     */
    public function checksProvider()
    {
        return [
            'checks.1' => [
                [],
                Response::HTTP_OK, 17,
            ],
            'checks.2' => [
                ['id' => 'core:php_version'],
                Response::HTTP_OK, 1,
            ],
            'checks.3' => [
                ['check' => 'core:php_version'],
                Response::HTTP_OK, 1,
            ],
            'checks.4' => [
                ['id[1]' => 'core:php_version'],
                Response::HTTP_OK, 1,
            ],
            'checks.5' => [
                ['check[1]' => 'core:php_version'],
                Response::HTTP_OK, 1,
            ],
            'group.1' => [
                ['group' => 'test:success'],
                Response::HTTP_OK, 3,
            ],
            'tag.1' => [
                ['tag' => 'all'],
                Response::HTTP_OK, 17,
            ],
         ];
    }

    /**
     * @return array [$params, $code, $res]
     */
    public function checkProvider()
    {
        return [
            'check.1' => [
                ['id' => 'core:php_version'],
                Response::HTTP_OK, ['id' => 'core:php_version'],
            ],
            'check.2' => [
                ['id' => 'test:success:check'],
                Response::HTTP_OK, [
                    'id' => 'test:success:check',
                    'group' => 'test:success',
                    'tags' => ['all', 'ok', 'test:all'],
                ],
            ],
            'not_exist.2' => [
                ['id' => 'not_exist'],
                Response::HTTP_NOT_FOUND, [],
            ],
        ];
    }
}
