<?php

namespace Ewave\Pronto\ProntoApi;

use Ewave\AI\Model\Lib\Connector\CurlHttpClient\RestApi;
use Ewave\AI\Model\Engine\Processor\ProcessorAbstract;

interface RequestBuilderInterface
{
    /**
     * @param ProcessorAbstract $process
     * @return $this
     */
    public function build(ProcessorAbstract $process);

    /**
     * @return RestApi
     */
    public function getHttpClient();

    /**
     * @return int
     */
    public function getStoreId();
}
