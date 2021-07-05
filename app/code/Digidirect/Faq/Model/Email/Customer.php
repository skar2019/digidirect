<?php

namespace Digidirect\Faq\Model\Email;

use Digidirect\Faq\Model\Faq;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\ScopeInterface as SI;

class Customer
{
    const XML_PATH_EMAIL_TEMPLATE = 'digidirect_faq/notification/customer_answer_template';
    const XML_PATH_EMAIL_SENDER = 'digidirect_faq/notification/sender';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * Admin constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param TransportBuilder $transportBuilder
     */
    public function __construct(ScopeConfigInterface $scopeConfig, TransportBuilder $transportBuilder)
    {
        $this->scopeConfig = $scopeConfig;
        $this->transportBuilder = $transportBuilder;
    }

    /**
     * @param Faq $faq
     * @return void
     */
    public function sendEmail(Faq $faq)
    {
        $transport = $this->transportBuilder
            ->setTemplateIdentifier($this->scopeConfig->getValue(self::XML_PATH_EMAIL_TEMPLATE, SI::SCOPE_STORE))
            ->setTemplateOptions(
                [
                    'area' => Area::AREA_FRONTEND,
                    'store' => $faq->getFromStore(),
                ]
            )
            ->setTemplateVars(['data' => $faq])
            ->setFrom($this->scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, SI::SCOPE_STORE))
            ->addTo($faq->getCustomerEmail())
            ->getTransport();

        $transport->sendMessage();
    }
}
