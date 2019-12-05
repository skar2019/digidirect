<?php

namespace Ewave\Digi\Plugin\Ewave\Pronto\ProntoApi;

use Ewave\AI\Model\Engine\Processor\ProcessorAbstract;

/**
 * Class RequestBuilder
 * @package Ewave\Digi\Plugin\Ewave\Pronto\ProntoApi
 */
class RequestBuilder
{
    /**
     * @var int
     */
    private $timeout;

    /**
     * RequestBuilder constructor.
     * @param int $timeout
     */
    public function __construct(
        int $timeout = 180
    )
    {
        $this->timeout = $timeout;
    }

    /**
     * @param \Ewave\Pronto\ProntoApi\RequestBuilder $subject
     * @param \Ewave\Pronto\ProntoApi\RequestBuilder $result
     * @param ProcessorAbstract $process
     * @return \Ewave\Pronto\ProntoApi\RequestBuilder
     * @throws \Exception
     */
    public function afterBuild(
        \Ewave\Pronto\ProntoApi\RequestBuilder $subject,
        \Ewave\Pronto\ProntoApi\RequestBuilder $result,
        ProcessorAbstract $process
    )
    {
        /**
         * @var $adapter \Zend\Http\Client\Adapter\Curl
         */
        $zendHttpClient = $result->getHttpClient()->getHttpClient();
        $adapter = $zendHttpClient->getAdapter();
        $adapter->setCurlOption(CURLOPT_TIMEOUT, $this->getTimeout());
        $adapter->setOptions(['timeout' => $this->getTimeout()]);
        return $result;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }
}