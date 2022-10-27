<?php

namespace Digidirect\JaiTestEmail\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class CustomerLoginSuccess implements ObserverInterface
{
    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        // If customer data is empty then doesn't need to process
        if (!$customer) {
            return $this;
        }

        $firstname = "Jireh";
        $lastname = "Capao";

        /* Receiver Detail */
        $receiverInfo = [
            'name' => 'Dev_Jireh',
            'email' => 'dev4@digidirect.com.au'
        ];

        // $store = $this->storeManager->getStore();

        // $templateParams = ['store' => $store, 'customer' => $customer, 'administrator_name' => $receiverInfo['name']];

        // $sender = [
        //     'name' => $this->_escaper->escapeHtml($post['name']),
        //     'email' => $this->_escaper->escapeHtml($post['email']),
        // ];

        $senderDetails = "
        <div>
        <b>".$firstname."</b>
        <i>".$lastname."</i>
        </div>";

        $transport = $this->transportBuilder->setTemplateIdentifier(
            'digisecond_sendingemail_template'
        )->setTemplateOptions(
            ['area' => 'frontend', 'store' => $store->getId()]
        )->addTo(
            $receiverInfo['email'], $receiverInfo['name']
        )->setTemplateVars(
            $senderDetails
        )->setFrom(
            'general'
        )->getTransport();

        try {
            // Send an email
            $transport->sendMessage();
        } catch (\Exception $e) {
            // Write a log message whenever get errors
            $this->logger->critical($e->getMessage());
        }
        return $this;
    }
}
