<?php

namespace Digidirect\Faq\Model\Email;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\ScopeInterface as SI;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Admin
 *
 * @package Digidirect\Faq\Model\Email
 */
class Admin
{
    const XML_PATH_EMAIL_TEMPLATE = 'digidirect_faq/notification/template';
    const XML_PATH_EMAIL_SENDER = 'digidirect_faq/notification/sender';
    const XML_PATH_EMAIL_RECIPIENT = 'digidirect_faq/notification/receiver';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Admin constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
    }

    /**
     * @param DataObject $postObject
     * @return void
     */
    public function sendEmail(DataObject $postObject)
    {

        $emailsList = $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_RECIPIENT,
            SI::SCOPE_STORE
        );

        $emailsList = explode(',', $emailsList);
        $emailsList = array_map('trim', $emailsList);
        foreach ($emailsList as $key => $email) {
            if (!$email) {
                unset($emailsList[$key]);
            }
        }

        if (empty($emailsList)) {
            return;
        }

        $transport = $this->transportBuilder
            ->setTemplateIdentifier(
                $this->scopeConfig->getValue(
                    self::XML_PATH_EMAIL_TEMPLATE,
                    SI::SCOPE_STORE
                )
            )
            ->setTemplateOptions(
                [
                    'area' => Area::AREA_FRONTEND,
                    'store' => $this->storeManager->getStore()->getId(),
                ]
            )
            ->setTemplateVars(['data' => $postObject])
            ->setFrom($this->scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, SI::SCOPE_STORE))
            ->addTo($emailsList)
            ->setReplyTo($postObject->getCustomerEmail())
            ->getTransport();

        $transport->sendMessage();
    }
}
