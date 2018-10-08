<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tvi\MonitorBundle\Exception\HttpException;
use Tvi\MonitorBundle\Reporter\ReporterManager;
use Tvi\MonitorBundle\Runner\RunnerManager;

/**
 * @property RunnerManager   $runnerManager
 * @property ReporterManager $reporterManager
 * @property Serializer      $serializer
 *
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
trait ApiInfoCheckTrait
{
    public function checkInfoAction(Request $request, $id): JsonResponse
    {
        try {
            list($checks, $groups, $tags) = $this->getFilterParams($request);

            $checks = $this->runnerManager->findChecks($id);
            if (1 === \count($checks)) {
                $check = current($checks);
                $json = $this->serializer->serialize($check, 'json');

                return JsonResponse::fromJsonString($json);
            }

            throw new NotFoundHttpException(sprintf('Check "%s" not found', $id));
        } catch (NotFoundHttpException $e) {
            $e = new HttpException(404, $e->getMessage());
            $json = $this->serializer->serialize($e->toArray(), 'json');

            return JsonResponse::fromJsonString($json, $e->getStatusCode());
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }
    }

    public function checkInfosAction(Request $request): JsonResponse
    {
        try {
            list($checks, $groups, $tags) = $this->getFilterParams($request);

            $checks = $this->runnerManager->findChecks($checks, $groups, $tags);
            $checks = array_values($checks);
            $json = $this->serializer->serialize($checks, 'json');

            return JsonResponse::fromJsonString($json);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }
    }
}
