<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\http\GuzzleHttpService;

use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use GuzzleHttp\Message\Request as GuzzleRequest;
use GuzzleHttp\Message\RequestInterface as GuzzleRequestInterface;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface as PsrRequestInterface;
use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\Success;

class GuzzleHttpService extends \ZendDiagnostics\Check\GuzzleHttpService
{
    protected $setData = false;

    /**
     * @param string|PsrRequestInterface|GuzzleRequestInterface $requestOrUrl
     *                                                                        The absolute url to check, or a
     *                                                                        fully-formed request instance
     * @param array                                             $headers      An array of headers used to create the
     *                                                                        request
     * @param array                                             $options      An array of guzzle options to use when
     *                                                                        sending the request
     * @param int                                               $statusCode   The response status code to check
     * @param null                                              $content      The response content to check
     * @param null|GuzzleClientInterface                        $guzzle       Instance of guzzle to use
     * @param string                                            $method       The method of the request
     * @param mixed                                             $body         The body of the request (used for POST, PUT
     *                                                                        and DELETE requests)
     * @param bool                                              $setData      set data to result
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        $requestOrUrl,
        array $headers = [],
        array $options = [],
        $statusCode = 200,
        $content = null,
        $guzzle = null,
        $method = 'GET',
        $body = null,
        $setData = false)
    {
        parent::__construct($requestOrUrl, $headers, $options, $statusCode, $content, $guzzle, $method, $body);

        $this->setData = $setData;
    }

    /**
     * @see ZendDiagnostics\CheckInterface::check()
     */
    public function check()
    {
        // GuzzleHttp\Message\RequestInterface only exists in v4 and v5.
        return class_exists(GuzzleRequest::class)
            ? $this->performLegacyGuzzleRequest()
            : $this->performGuzzleRequest();
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return \ZendDiagnostics\Result\ResultInterface
     */
    protected function performGuzzleRequest()
    {
        $response = $this->guzzle->send($this->request, array_merge([
            'exceptions' => false,
        ], $this->options));

        return $this->analyzeResponse($response);
    }

    /**
     * @param \GuzzleHttp\Message\ResponseInterface|Psr\Http\Message\ResponseInterface $response
     *
     * @return \ZendDiagnostics\Result\ResultInterface
     */
    protected function analyzeResponse($response)
    {
        $result = $this->analyzeStatusCode((int) $response->getStatusCode());
        if ($result instanceof Failure) {
            return $result;
        }

        $result = $this->analyzeResponseContent((string) $response->getBody());
        if ($result instanceof Failure) {
            return $result;
        }

        $data = null;
        if ($this->setData) {
            $data = (string) $response->getBody();
        }

        return new Success(null, $data);
    }

    /**
     * @param int $statusCode
     *
     * @return bool|FailureInterface Returns boolean true when successful, and
     *                               a FailureInterface instance otherwise
     */
    protected function analyzeStatusCode($statusCode)
    {
        return $this->statusCode === $statusCode
            ? true
            : new Failure(sprintf(
                'Status code %s does not match %s in response from %s',
                $this->statusCode,
                $statusCode,
                $this->getUri()
            ));
    }

    /**
     * @param string $content
     *
     * @return bool|FailureInterface Returns boolean true when successful, and
     *                               a FailureInterface instance otherwise
     */
    protected function analyzeResponseContent($content)
    {
        return !$this->content || false !== mb_strpos($content, $this->content)
            ? true
            : new Failure(sprintf(
                'Content %s not found in response from %s',
                $this->content,
                $this->getUri()
            ));
    }

    /**
     * @return string
     */
    protected function getUri()
    {
        return $this->request instanceof PsrRequestInterface
            ? (string) $this->request->getUri() // guzzle 6
            : $this->request->getUrl();         // guzzle 4 and 5
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return \ZendDiagnostics\Result\ResultInterface
     */
    private function performLegacyGuzzleRequest()
    {
        $response = $this->guzzle->send($this->request);

        return $this->analyzeResponse($response);
    }
}
