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
use Tvi\MonitorBundle\Reporter\ReporterManager;
use Tvi\MonitorBundle\Runner\RunnerManager;

/**
 * @property RunnerManager   $runnerManager
 * @property ReporterManager $reporterManager
 * @property Serializer      $serializer
 *
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
trait ApiInfoTrait
{
    public function checkInfoListAction(Request $request): JsonResponse
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

    public function groupInfoListAction(Request $request): JsonResponse
    {
        try {
            list($_, $groups, $_) = $this->getFilterParams($request);
            $groups = $this->runnerManager->findGroups($groups);
            $groups = array_values($groups);
            $json = $this->serializer->serialize($groups, 'json');

            return JsonResponse::fromJsonString($json);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }
    }

    public function tagInfoListAction(Request $request): JsonResponse
    {
        try {
            list($_, $_, $tags) = $this->getFilterParams($request);
            $tags = array_values($this->runnerManager->findTags($tags));
            $json = $this->serializer->serialize($tags, 'json');

            return JsonResponse::fromJsonString($json);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }
    }
}
