<?php

namespace Ewave\Pronto\ProntoApi;

use Magento\Store\Model\Store;
use Ewave\AI\Model\Engine\Processor\ProcessorAbstract;
use Ewave\AI\Model\Lib\Mapping\MapperInterface;
use Ewave\AI\Model\Lib\Mapping\MapperTemplateFilter;

abstract class ResponseHandler implements ResponseHandlerInterface
{
    /**
     * @var MapperInterface
     */
    protected $mapper;

    /**
     * @var int
     */
    protected $storeId = Store::DEFAULT_STORE_ID;

    /**
     * @var RequestBuilderInterface
     */
    protected $requestBuilder;

    /**
     * @var ProcessorAbstract
     */
    protected $process;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var bool
     */
    protected $isInitialized = false;

    /**
     * ResponseHandler constructor.
     * @param MapperInterface $mapper
     */
    public function __construct(
        MapperInterface $mapper = null
    ) {
        $this->mapper = $mapper;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function mapData($data)
    {
        if ($this->mapper instanceof MapperInterface) {
            if ($this->mapper instanceof MapperTemplateFilter) {
                $this->mapper->setStoreId($this->storeId);
            }
            $data = $this->mapper->map($data);
        }
        return $data;
    }

    /**
     * @param ProcessorAbstract $process
     * @param RequestBuilderInterface $requestBuilder
     * @return $this
     */
    public function init(ProcessorAbstract $process, RequestBuilderInterface $requestBuilder)
    {
        $this->process = $process;
        $this->requestBuilder = $requestBuilder;
        $this->storeId = $requestBuilder->getStoreId();
        $this->logger = $process->getLogger();
        $this->isInitialized = true;
        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function throwNotInitializedExceptionIfNotInitialized()
    {
        if (!$this->isInitialized) {
            throw new \Exception('Request was not built');
        }
        return $this;
    }
}
