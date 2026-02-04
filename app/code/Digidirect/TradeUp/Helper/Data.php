<?php
namespace Digidirect\TradeUp\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    const XML_PATH_ENABLED = 'digidirect_tradeup/general/enabled';
    const XML_PATH_RECIPIENT_EMAIL = 'digidirect_tradeup/general/recipient_email';
    const XML_PATH_EMAIL_TEMPLATE_COMPANY = 'digidirect_tradeup/general/email_template_company';
    const XML_PATH_EMAIL_TEMPLATE_CUSTOMER = 'digidirect_tradeup/general/email_template_customer';

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Context $context
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->storeManager = $storeManager;
    }

    /**
     * Check if Trade Up functionality is enabled
     *
     * @return bool
     */
    public function isTradeUPFromEnabled()
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );

        return $value === null ? true : (bool) $value;
    }

    /**
     * Get recipient email
     *
     * @return string
     */
    public function getRecipientEmail()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_RECIPIENT_EMAIL,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get email template ID
     *
     * @return string
     */
    public function getEmailTemplateCompany()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_TEMPLATE_COMPANY,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get email template ID
     *
     * @return string
     */
    public function getEmailTemplateCustomer()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_TEMPLATE_CUSTOMER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Send Trade Up email
     *
     * @param array $formData
     * @param array $fileData
     * @return bool
     */
    public function sendTradeUpEmail($formData, $fileData = [])
    {
        try {
            $this->inlineTranslation->suspend();

            $templateVars = [
                'fullname' => $formData['fullname'],
                'email' => $formData['email'],
                'phonenumber' => $formData['phonenumber'],
                'rate' => $formData['rate'],
                'accessories' => $formData['accessories'],
                'miscellaneousaccessories' => $formData['miscellaneousaccessories'],
                'shuttercount' => $formData['shuttercount']
            ];

            $storeId = $this->storeManager->getStore()->getId();
            $recipientCompanyEmail = $this->getRecipientEmail();
            $templateIdCompany = $this->getEmailTemplateCompany();

            $this->transportBuilder
                ->setTemplateIdentifier($templateIdCompany)
                ->setTemplateOptions([
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $storeId
                ])
                ->setTemplateVars($templateVars)
                ->setFromByScope('general')
                ->addTo($recipientCompanyEmail)
                ->setReplyTo($formData['email'], $formData['fullname']);

            $transportCompany = $this->transportBuilder->getTransport();
            $transportCompany->sendMessage();

            $recipientCustomerEmail = $formData['email'];
            $templateIdCustomer = $this->getEmailTemplateCustomer();

            $this->transportBuilder
                ->setTemplateIdentifier($templateIdCustomer)
                ->setTemplateOptions([
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $storeId
                ])
                ->setTemplateVars($templateVars)
                ->setFromByScope('general')
                ->addTo($recipientCustomerEmail)
                ->setReplyTo($formData['email'], $formData['fullname']);

            $transportCustomer = $this->transportBuilder->getTransport();

            $transportCustomer->sendMessage();

            $this->inlineTranslation->resume();

            return true;
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->_logger->error('Trade Up Email Error: ' . $e->getMessage());
            return false;
        }
    }
}

