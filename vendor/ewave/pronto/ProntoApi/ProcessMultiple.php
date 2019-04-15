<?php

namespace Ewave\Pronto\ProntoApi;

/**
 * Class ProcessMultiple
 * @property ResponseHandlerMultipleInterface $responseHandler
 * @package Ewave\Pronto\ProntoApi
 */
abstract class ProcessMultiple extends Process
{
    /**
     * @return $this
     */
    abstract protected function reInitRunOptions();

    /**
     * @param mixed $response
     * @return mixed
     */
    abstract protected function updateProcessedData($response);

    /**
     * @return bool
     */
    abstract protected function isRequestNeeded();

    /**
     * @param mixed $response
     * @return array
     */
    abstract protected function getResponseData($response);

    /**
     * Process constructor.
     * @param RequestBuilderInterface $requestBuilder
     * @param ResponseHandlerMultipleInterface $responseHandler
     * @param null $initParams
     */
    public function __construct(
        RequestBuilderInterface $requestBuilder,
        ResponseHandlerMultipleInterface $responseHandler,
        $initParams = null
    ) {
        parent::__construct($requestBuilder, $responseHandler, $initParams);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function process()
    {
        $this->reInitRunOptions();
        try {
            while ($this->isRequestNeeded()) {
                $this->requestBuilder->build($this);
                $this->responseHandler->init($this, $this->requestBuilder);
                $httpClient = $this->requestBuilder->getHttpClient();
                $response = $httpClient->doRequest();
                $this->responseHandler->handle($this->getResponseData($response));
                $this->updateProcessedData($response);
                $this->reInitRunOptions();
            }
            $this->responseHandler->finalize();
        } catch (\Throwable $e) {
            $this->throwException($e->getMessage(), $e, 1);
        }

        return true;
    }
}
