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
    public function checkInfoAction(Request $request, $id): Response
    {
        try {
            $checks = $this->runnerManager->findChecks($id);
            if (1 === \count($checks)) {
                $check = current($checks);

                return $this->creatResponse($check, Response::HTTP_OK, true);
            }

            throw new NotFoundHttpException(sprintf('Check "%s" not found', $id));
        } catch (NotFoundHttpException $e) {
            $e = new HttpException($e->getStatusCode(), $e->getMessage());

            return $this->creatResponse($e->toArray(), $e->getStatusCode(), true);
        } catch (\Exception $e) {
            return $this->creatResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function checkInfosAction(Request $request): Response
    {
        try {
            list($ids, $checks, $groups, $tags) = $this->getFilterParams($request);
            $checks = $checks ? $checks : $ids;

            $checks = $this->runnerManager->findChecks($checks, $groups, $tags);
            $checks = array_values($checks);

            return $this->creatResponse($checks, Response::HTTP_OK, true);
        } catch (\Exception $e) {
            return $this->creatResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
