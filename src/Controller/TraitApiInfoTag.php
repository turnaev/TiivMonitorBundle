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
trait TraitApiInfoTag
{
    public function tagInfoAction(Request $request, string $id, string $version): Response
    {
        try {
            $tags = $this->runnerManager->findTags($id);

            if (1 === \count($tags)) {
                $tag = current($tags);

                return $this->creatResponse($tag, Response::HTTP_OK, true);
            }

            throw new NotFoundHttpException(sprintf('Tag "%s" not found', $id));
        } catch (NotFoundHttpException $e) {
            $e = new HttpException($e->getStatusCode(), $e->getMessage());

            return $this->creatResponse($e->toArray(), $e->getStatusCode(), true);
        } catch (\Exception $e) {
            return $this->creatResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function tagInfosAction(Request $request): Response
    {
        try {
            list($ids, $_, $_, $tags) = $this->getFilterParams($request);
            $tags = $tags ? $tags : $ids;

            $tags = array_values($this->runnerManager->findTags($tags));

            return $this->creatResponse($tags, Response::HTTP_OK, true);
        } catch (\Exception $e) {
            return $this->creatResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
