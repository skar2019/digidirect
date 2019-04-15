<?php

namespace Ewave\Newsletter\Model;

use Ewave\Utilities\Model\CustomTemplateVarsInterface;

/**
 * Class MailTemplateCustomVar
 *
 * @package Ewave\Newsletter\Model
 */
class MailTemplateCustomVar implements CustomTemplateVarsInterface
{
    const TEMPLATE_VAR_SUBSCRIBER = 'subscriber';

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $subscriberFactory;

    /**
     * @var string
     */
    protected $templateVarSubscriber;

    /**
     * TransportBuilder constructor.
     *
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param string $templateVarSubscriber
     */
    public function __construct(
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        $templateVarSubscriber = self::TEMPLATE_VAR_SUBSCRIBER
    ) {
        $this->subscriberFactory = $subscriberFactory;
        $this->templateVarSubscriber = $templateVarSubscriber;
    }

    /**
     * Get template custom variables
     *
     * @param \Magento\Framework\Mail\Template\TransportBuilder $subject
     * @return array
     */
    public function getVars(\Magento\Framework\Mail\Template\TransportBuilder $subject, array $templateVars = null)
    {
        if (isset($templateVars[$this->templateVarSubscriber])) {
            return [];
        }

        $email = $subject->getTo();
        $subscriber = $this->subscriberFactory->create()->loadByEmail($email);
        return [
            $this->templateVarSubscriber => $subscriber
        ];
    }
}
