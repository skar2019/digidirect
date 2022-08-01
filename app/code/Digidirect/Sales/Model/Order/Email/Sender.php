<?php

namespace Digidirect\Sales\Model\Order\Email\Sender;

use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Container\OrderIdentity;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\Sales\Model\Order\Email\Sender;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Framework\Event\ManagerInterface;
use Magento\Quote\Api\Data\ShippingMethodInterface;

class Sender extends \Magento\Sales\Model\Order\Email\Sender
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerFactory;

    protected $emailTemplate;

    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Email\Model\BackendTemplate $emailTemplate,
        Template $templateContainer,
        OrderIdentity $identityContainer,
        \Magento\Sales\Model\Order\Email\SenderBuilderFactory $senderBuilderFactory,
        \Psr\Log\LoggerInterface $logger,
        Renderer $addressRenderer,
        PaymentHelper $paymentHelper,
        OrderResource $orderResource,
        \Magento\Framework\App\Config\ScopeConfigInterface $globalConfig,
        ManagerInterface $eventManager,
        ShippingMethodInterface $shippingMethod)
    {
        parent::__construct($templateContainer, $identityContainer, $senderBuilderFactory, $logger, $addressRenderer, $paymentHelper, $orderResource, $globalConfig, $eventManager);
        $this->customerFactory = $customerFactory;
        $this->emailTemplate = $emailTemplate;
        $this->shippingMethod = $shippingMethod;
    }

    /**
     * Populate order email template with customer information.
     *
     * @param Order $order
     * @return void
     */
    protected function prepareTemplate(Order $order)
    {
        parent::prepareTemplate($order);
        $methodTitle = $this->shippingMethod->getMethodTitle();
        echo $this->console_log("Preference Working!");
        
        $this->templateContainer->setTemplateOptions($this->getTemplateOptions());

        if ($order->getCustomerIsGuest()) {
            $templateId = $this->identityContainer->getGuestTemplateId();
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $email_template = $this->emailTemplate->load('Custom Header', 'template_code');
            $customerDisTemplate  =  $email_template->getId();
            
            $isApproved = $this->isCustomerApproved($order->getCustomerId());
            if($isApproved){
                $templateId = $this->identityContainer->getTemplateId();
            }else{
                $templateId = $customerDisTemplate;
            }

            $customerName = $order->getCustomerName();
        }

        $this->identityContainer->setCustomerName($customerName);
        $this->identityContainer->setCustomerEmail($order->getCustomerEmail());
        $this->templateContainer->setTemplateId($templateId);
    }
    
    private function isCustomerApproved($customerId)
    {
        try {
            $customer = $this->customerFactory->create()->load($customerId);
            $isApprove = $customer->getApproveAccount();
            if($isApprove){
                return true;
            }
        } catch (NoSuchEntityException | LocalizedException $e) {
            return false;
        }
        return false;
    }
    
    function console_log($output, $with_script_tags = true) {
        $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . 
    ');';
        if ($with_script_tags) {
            $js_code = '<script>' . $js_code . '</script>';
        }
        echo $js_code;
    }
}