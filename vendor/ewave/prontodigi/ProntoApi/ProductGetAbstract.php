<?php

namespace Ewave\ProntoDigi\ProntoApi;

use Ewave\Pronto\ProntoApi\ProcessMultiple;
use Ewave\ProntoDigi\ProntoApi\Constants\ProductsGetRequest;

/**
 * Class ProductGetAbstract
 * @package Ewave\ProntoDigi\ProntoApi
 */
class ProductGetAbstract extends ProcessMultiple
{
    const FLAG_PROCESS_DATA = 'pronto_process_get_start_item';
    const FLAG_PROCESS_DATA_PROCESSED_CODES = 'processed_codes';
    const FLAG_PROCESS_DATA_LAST_CODE = 'last_code';
    const FLAG_PROCESS_DATA_TERMINATE = 'terminate';

    const ROOT_NODE = 'stockmaster';
    const CONTAINER = 'stockcode';
    const IDENTIFIER = 'code';

    const LIMIT = 500;

    /**
     * @var array
     */
    protected $storage = [];

    /**
     * @return $this|ProcessMultiple
     */
    protected function reInitRunOptions()
    {
        $flagData = $this->getStorageData(self::FLAG_PROCESS_DATA, []);
        if (!$flagData) {
            $start = '0';
        } else {
            $start = isset($flagData[self::FLAG_PROCESS_DATA_LAST_CODE]) ?
                $flagData[self::FLAG_PROCESS_DATA_LAST_CODE] + 1 : null;
        }

        $this->_runOptions = [
            ProductsGetRequest::START_ITEM => $start
        ];

        return $this;
    }

    /**
     * @param array $response
     * @return $this
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function updateProcessedData($response)
    {
        $response = $this->standardizeResponse($response);
        if (!is_array($response)) {
            throw new \Exception(sprintf('Response is not valid `%s`', $response));
        }
        $flagData = $this->getStorageData(self::FLAG_PROCESS_DATA, []);

        if (!array_key_exists(self::ROOT_NODE, $response)
            || !is_array($response[self::ROOT_NODE])
            || !array_key_exists(self::CONTAINER, $response[self::ROOT_NODE])
        ) {
            return $this->saveTerminateFlag($flagData);
        }
        $processedProducts = $response[self::ROOT_NODE][self::CONTAINER];

        if (!$processedProducts) {
            return $this->saveTerminateFlag($flagData);
        }

        $flagData = $this->updateProcessedCodes($processedProducts, $flagData);
        $flagData = $this->updateLastProcessedCode($flagData);

        $this->saveStorageCode(self::FLAG_PROCESS_DATA, $flagData);

        return $this;
    }

    /**
     * @param array $processedProducts
     * @param array $flagData
     *
     * @return array
     */
    protected function updateProcessedCodes(array $processedProducts, array $flagData = [])
    {
        $productCodesAggregator = array_column($processedProducts, self::IDENTIFIER);

        if ($productCodesAggregator) {
            $flagData[self::FLAG_PROCESS_DATA_PROCESSED_CODES] = array_merge(
                ($flagData[self::FLAG_PROCESS_DATA_PROCESSED_CODES] ?? []),
                $productCodesAggregator
            );
        }

        return $flagData;
    }

    /**
     * @param array $flagData
     * @return array
     */
    protected function updateLastProcessedCode(array $flagData = [])
    {
        $processedProducts = $flagData[self::FLAG_PROCESS_DATA_PROCESSED_CODES] ?? [];
        $lastProductCode = end($processedProducts);
        if ($lastProductCode) {
            $flagData[self::FLAG_PROCESS_DATA_LAST_CODE] = $lastProductCode;
        }

        return $flagData;
    }

    /**
     * @param array $flagData
     *
     * @return $this
     */
    protected function saveTerminateFlag(array $flagData)
    {
        $flagData[self::FLAG_PROCESS_DATA_TERMINATE] = true;
        $this->saveStorageCode(self::FLAG_PROCESS_DATA, $flagData);
        return $this;
    }

    /**
     * @return bool
     */
    protected function isRequestNeeded()
    {
        $flagData = $this->getStorageData(self::FLAG_PROCESS_DATA, []);
        return !($flagData[self::FLAG_PROCESS_DATA_TERMINATE] ?? false);
    }

    /**
     * @param mixed $response
     * @throws \Ewave\AI\Model\Engine\Exception\EngineException
     * @return $this
     */
    protected function getResponseData($response)
    {
        $responseToLog = is_array($response) ? $response : [$response];
        $this->getLogger()->info(__('Result'), $responseToLog, \Ewave\AI\Model\Logger\Logger::LOG_PLACE_FILE);

        if (!empty($responseToLog[self::ROOT_NODE])
            && is_array($responseToLog[self::ROOT_NODE])
        ) {
            $responseToLog[self::ROOT_NODE] = 'Download log file to see products list.';
        }
        $this->getLogger()->info(__('Result'), $responseToLog, \Ewave\AI\Model\Logger\Logger::LOG_PLACE_DB);

        if (!is_array($response)
            || !isset($response[self::ROOT_NODE])
            || !is_array($response[self::ROOT_NODE])
            || !isset($response[self::ROOT_NODE][self::CONTAINER])
            || !is_array($response[self::ROOT_NODE][self::CONTAINER])
        ) {
            $this->getLogger()->warning(
                __('Invalid data structure.'),
                [],
                \Ewave\AI\Model\Logger\Logger::LOG_PLACE_DB
            );
            return [];
        }

        $response = $this->standardizeResponse($response);
        return $response[self::ROOT_NODE][self::CONTAINER];
    }

    /**
     * @inheritdoc
     */
    public function getStorageData($code, $default = null)
    {
        return $this->storage[$code] ?? $default;
    }

    /**
     * @inheritdoc
     */
    public function saveStorageCode($code, $value)
    {
        $this->storage[$code] = $value;

        return true;
    }

    /**
     * @param mixed $response
     * @return mixed
     */
    protected function standardizeResponse($response)
    {
        if (!empty($response[self::ROOT_NODE][self::CONTAINER])) {
            $itemKey = key($response[self::ROOT_NODE][self::CONTAINER]);
            if (!is_numeric($itemKey)) {
                $response[self::ROOT_NODE][self::CONTAINER] = [$response[self::ROOT_NODE][self::CONTAINER]];
            }
        }
        return $response;
    }
}
