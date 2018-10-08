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
trait ApiInfoGroupTrait
{
    public function groupInfoAction(Request $request, string $id): JsonResponse
    {
        try {
            $groups = $this->runnerManager->findGroups($id);
            $groups = array_values($groups);

            if (1 === \count($groups)) {
                $group = current($groups);
                $json = $this->serializer->serialize($group, 'json');

                return JsonResponse::fromJsonString($json);
            }

            throw new NotFoundHttpException(sprintf('Group "%s" not found', $id));
        } catch (NotFoundHttpException $e) {
            $e = new HttpException(404, $e->getMessage());
            $json = $this->serializer->serialize($e->toArray(), 'json');

            return JsonResponse::fromJsonString($json, $e->getStatusCode());
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }
    }

    public function groupInfosAction(Request $request): JsonResponse
    {
        try {
            $ids = $this->getFilterIds($request);
            $groups = array_values($this->runnerManager->findGroups($ids));
            $json = $this->serializer->serialize($groups, 'json');

            return JsonResponse::fromJsonString($json);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }
    }
}
