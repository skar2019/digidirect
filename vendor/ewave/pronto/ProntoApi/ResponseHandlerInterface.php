<?php

namespace Ewave\Pronto\ProntoApi;

use Ewave\AI\Model\Engine\Processor\ProcessorAbstract;

interface ResponseHandlerInterface
{
    /**
     * @param ProcessorAbstract $process
     * @param RequestBuilderInterface $requestBuilder
     * @return $this
     */
    public function init(ProcessorAbstract $process, RequestBuilderInterface $requestBuilder);

    /**
     * @param array $response
     * @return $this
     */
    public function handle(array $response);
}
