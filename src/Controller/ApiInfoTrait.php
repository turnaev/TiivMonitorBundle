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
use Tvi\MonitorBundle\Exception\HttpException;
use Tvi\MonitorBundle\Reporter\ReporterManager;
use Tvi\MonitorBundle\Runner\RunnerManager;

/**
 * @property RunnerManager   $runnerManager
 * @property ReporterManager $reporterManager
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

            $data = [];
            foreach ($checks as $check) {
                $tags = $check->getTags();

                $d = ['check' => $check->getId(), 'label' => $check->getLabel(), 'group' => $check->getGroup(), 'tags' => $tags, 'Descr' => $check->getDescr()];
                $d = array_filter($d, static function ($v) {
                    return !empty($v);
                });
                $data[] = $d;
            }

            return new JsonResponse($data);
        } catch (\Exception $e) {
            $e = new HttpException(500, $e->getMessage());

            return new JsonResponse($e->toArray(), $e->getStatusCode());
        }
    }

    public function groupInfoListAction(Request $request): JsonResponse
    {
        try {
            list($_, $groups, $_) = $this->getFilterParams($request);
            $groups = $this->runnerManager->findGroups($groups);

            return new JsonResponse($groups);
        } catch (\Exception $e) {
            $e = new HttpException(500, $e->getMessage());

            return new JsonResponse($e->toArray(), $e->getStatusCode());
        }
    }

    public function tagInfoListAction(Request $request): JsonResponse
    {
        try {
            try {
                list($_, $_, $tags) = $this->getFilterParams($request);
                $tags = $this->runnerManager->findTags($tags);
v($tags);
                exit;
                return new JsonResponse($tags);

            } catch (\Exception $e) {
                $e = new HttpException(500, $e->getMessage());

                return new JsonResponse($e->toArray(), $e->getStatusCode());
            }
        } catch (\Exception $e) {
            $e = new HttpException(500, $e->getMessage());

            return new JsonResponse($e->toArray(), $e->getStatusCode());
        }
    }
}
