<?php

namespace Ewave\AbstractGiftCard\Service\Http\Client;

use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\HTTP\ZendClient;
use Ewave\AbstractGiftCard\Service\Http\ClientInterface;
use Ewave\AbstractGiftCard\Service\Http\ConverterInterface;
use Ewave\AbstractGiftCard\Service\Http\TransferInterface;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class Zend
 * @package Ewave\AbstractGiftCard\Service\Http\Client
 * @api
 */
class Zend implements ClientInterface
{
    /**
     * @var ZendClientFactory
     */
    private $_clientFactory;

    /**
     * @var ConverterInterface | null
     */
    private $_converter;

    /**
     * @var Logger
     */
    private $_logger;

    /**
     * @param ZendClientFactory $clientFactory
     * @param Logger $logger
     * @param ConverterInterface | null $converter
     */
    public function __construct(
        ZendClientFactory $clientFactory,
        Logger $logger,
        ConverterInterface $converter = null
    ) {
        $this->_clientFactory = $clientFactory;
        $this->_converter = $converter;
        $this->_logger = $logger;
    }

    /**
     * {inheritdoc}
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        $log = [
            'request' => $transferObject->getBody(),
            'request_uri' => $transferObject->getUri()
        ];
        $result = [];
        /** @var ZendClient $client */
        $client = $this->_clientFactory->create();

        $client->setConfig($transferObject->getClientConfig());
        $client->setMethod($transferObject->getMethod());

        switch ($transferObject->getMethod()) {
            case \Zend_Http_Client::GET:
                $client->setParameterGet($transferObject->getBody());
                break;
            case \Zend_Http_Client::POST:
                $client->setParameterPost($transferObject->getBody());
                break;
            default:
                throw new \LogicException(
                    sprintf(
                        'Unsupported HTTP method %s',
                        $transferObject->getMethod()
                    )
                );
        }

        $client->setHeaders($transferObject->getHeaders());
        $client->setUrlEncodeBody($transferObject->shouldEncode());
        $client->setUri($transferObject->getUri());

        try {
            $response = $client->request();

            $result = $this->_converter
                ? $this->_converter->convert($response->getBody())
                : [$response->getBody()];
            $log['response'] = $result;
        } catch (\Zend_Http_Client_Exception $e) {
            throw new \Ewave\AbstractGiftCard\Service\Http\ClientException(
                __($e->getMessage())
            );
        } catch (\Ewave\AbstractGiftCard\Service\Http\ConverterException $e) {
            throw $e;
        } finally {
            $this->_logger->debug($log);
        }

        return $result;
    }
}
