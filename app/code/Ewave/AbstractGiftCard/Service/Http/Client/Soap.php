<?php

namespace Ewave\AbstractGiftCard\Service\Http\Client;

use Ewave\AbstractGiftCard\Api\AbstractGiftCardLoggerInterface as Logger;
use Ewave\AbstractGiftCard\Service\Http\ClientInterface;
use Ewave\AbstractGiftCard\Service\Http\ConverterInterface;
use Ewave\AbstractGiftCard\Service\Http\TransferInterface;
use Magento\Framework\Webapi\Soap\ClientFactory;

/**
 * Class Soap
 *
 * @package Ewave\AbstractGiftCard\Service\Http\Client
 * @api
 */
class Soap implements ClientInterface
{
    /**
     * @var Logger
     */
    private $_logger;

    /**
     * @var ConverterInterface | null
     */
    private $_converter;

    /**
     * @var ClientFactory
     */
    private $_clientFactory;

    /**
     * @param Logger $logger
     * @param ClientFactory $clientFactory
     * @param ConverterInterface | null $converter
     */
    public function __construct(
        Logger $logger,
        ClientFactory $clientFactory,
        ConverterInterface $converter = null
    ) {
        $this->_logger = $logger;
        $this->_converter = $converter;
        $this->_clientFactory = $clientFactory;
    }

    /**
     * Places request to gateway. Returns result as ENV array
     *
     * @param TransferInterface $transferObject
     * @return array
     * @throws \Ewave\AbstractGiftCard\Service\Http\ClientException
     * @throws \Ewave\AbstractGiftCard\Service\Http\ConverterException
     * @throws \Exception
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        $body = $transferObject->getBody();

        $this->_logger->debug(['request' => $body]);

        $client = $this->_clientFactory->create(
            $transferObject->getClientConfig()['wsdl'],
            array_merge(['trace' => true], $transferObject->getOptions())
        );

        try {
            if ($transferObject->getHeaders()) {
                $client->__setSoapHeaders($transferObject->getHeaders());
            }

            $response = $client->__soapCall($transferObject->getMethod(), [$body]);

            $result = $this->_converter
                ? $this->_converter->convert($response)
                : [$response];

            $this->_logger->debug(['response' => $result]);
        } catch (\Exception $e) {
            $this->_logger->debug(['trace' => $client->__getLastRequest()]);
            throw $e;
        }

        return $result;
    }
}
