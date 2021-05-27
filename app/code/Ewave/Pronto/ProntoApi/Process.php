<?php

namespace Ewave\Pronto\ProntoApi;

use Ewave\AI\Model\Engine\Processor\ProcessorInterface;
use Ewave\AI\Model\Engine\Processor\ProcessorAbstract;
use Ewave\AI\Model\Logger\Logger;
use Ewave\AI\Model\Engine\Processor\Exception\ProcessException;
use Ewave\AI\Model\Lib\Connector\CurlHttpClient\RestApiFactory;

/**
 * Class Process
 * @package Ewave\Mulesoft\MulesoftApi
 * @method \Ewave\AI\Model\Logger\Logger getLogger()
 */
class Process extends ProcessorAbstract implements ProcessorInterface
{
    /**
     * @var RequestBuilderInterface|null
     */
    protected $requestBuilder;

    /**
     * @var ResponseHandlerInterface|null
     */
    protected $responseHandler;

    /**
     * @var bool
     */
    protected $useQueue = true;

    /**
     * Process constructor.
     * @param RequestBuilderInterface $requestBuilder
     * @param ResponseHandlerInterface|null $responseHandler
     * @param null $initParams
     */
    public function __construct(
        RequestBuilderInterface $requestBuilder,
        ResponseHandlerInterface $responseHandler = null,
        $initParams = null
    ) {
        parent::__construct($initParams);
        $this->requestBuilder = $requestBuilder;
        $this->responseHandler = $responseHandler;
    }

    /**
     * @param mixed $runOption
     * @return array|mixed
     * @throws \Exception
     */
    protected function convertRunOptionToQueueData($runOption)
    {
        if (is_scalar($runOption)) {
            return $runOption;
        }

        if (is_array($runOption)) {
            foreach ($runOption as $k => $v) {
                $runOption[$k] = $this->convertRunOptionToQueueData($v);
            }
            return $runOption;
        }

        if (is_object($runOption)) {
            if ($runOption instanceof \Magento\Framework\Model\AbstractModel) {
                return $runOption->getId();
            }

            return $runOption;
        }

        throw new \Exception('Could not convert run options to queue');
    }

    /**
     * Get Data as string for Queue for run
     * need implement in processor id will used queue
     *
     * @return string
     */
    public function convertRunOptionsToQueueData()
    {
        $queueData = is_array($this->getRunOptions()) ? $this->getRunOptions() : [];
        foreach ($queueData as $k => $runOption) {
            $queueData[$k] = $this->convertRunOptionToQueueData($runOption);
        }
        return serialize($queueData);
    }

    /**
     * @param string $message
     * @param null $e
     * @param int $stopper
     * @throws ProcessException
     * @throws \Ewave\AI\Model\Engine\Exception\EngineException
     * @return void
     */
    protected function throwException($message, $e = null, $stopper = 0)
    {
        $this->getLogger()->critical($message, [], Logger::LOG_PLACE_FILE_AND_DB);
        if ($this->useQueue && !$this->isAdminRun && !$this->getQueueId()) {
            $this->setFlag(ProcessorAbstract::FLAG_PUT_TU_QUEUE, 1);
        }
        throw new ProcessException($message, 0, $e, $stopper);
    }

    /**
     * @param mixed $response
     * @throws \Ewave\AI\Model\Engine\Exception\EngineException
     * @return $this
     */
    protected function processResponseData($response)
    {
        $responseToLog = is_array($response) ? $response : [$response];
        $this->getLogger()->info(__('Result'), $responseToLog);
        if ($this->responseHandler instanceof ResponseHandlerInterface) {
            $this->responseHandler->init($this, $this->requestBuilder);
            $this->responseHandler->handle($response);
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getRunOptions()
    {
        $runOptions = parent::getRunOptions();
        return is_array($runOptions) ? $runOptions : [];
    }

    /**
     * @return bool
     */
    public function process()
    {
        //create and prepare HttpClient before doRequest
        $this->requestBuilder->build($this);
        $httpClient = $this->requestBuilder->getHttpClient();
        try {
            $response = $httpClient->doRequest();
            $this->processResponseData($response);
        } catch (\Throwable $e) {
            $this->throwException($e->getMessage(), $e, 1);
        }

        return true;
    }

    /**
     * @param string|array $identifiers
     * @return $this
     */
    public function addProcessIdentifiersToLog($identifiers)
    {
        $this->getLogger()->addIdentifyingParams($identifiers);
        return $this;
    }
}
