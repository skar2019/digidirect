<?php

namespace Digidirect\Sales\Model\Mail\Sender;

class OrderSender extends \Magento\Sales\Model\Order\Email\Sender\OrderSender
{
    /**
     * @var \Fooman\EmailAttachments\Model\AttachmentContainerInterface
     */
    protected $templateContainer;
    protected $_designerhelper;

    public function __construct(
        \Magento\Sales\Model\Order\Email\Container\Template $templateContainer,
        \Magento\Sales\Model\Order\Email\Container\OrderIdentity $identityContainer,
        \Magento\Sales\Model\Order\Email\SenderBuilderFactory $senderBuilderFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Sales\Model\ResourceModel\Order $orderResource,
        \Magento\Framework\App\Config\ScopeConfigInterface $globalConfig,
        \Magento\Framework\Event\ManagerInterface $eventManager
    ) {
        $this->templateContainer = $templateContainer;

        parent::__construct(
            $this->templateContainer,
            $identityContainer,
            $senderBuilderFactory,
            $logger,
            $addressRenderer,
            $paymentHelper,
            $orderResource,
            $globalConfig,
            $eventManager
        );
      /*  $this->attachmentContainer = $attachmentContainer;*/
    }

    public function send(\Magento\Sales\Model\Order $order, $forceSyncMode = false)
    {
        $items = $order->getAllVisibleItems();
        $IncrementId = $order->getIncrementId();
       

        $paymentMethod = $order->getPayment()->getMethod();

    

        //Define email template for each payment method
        switch ($paymentMethod) {
            case 'banktransfer' :
                $templateId = 'custom_template_cod';
                break;
            // Add cases if you have more payment methods
            default:
                $templateId = $order->getCustomerIsGuest() ? $this->identityContainer->getGuestTemplateId() : $this->identityContainer->getTemplateId();

        }

   	$this->templateContainer->setTemplateid(15);  
      
        return parent::send($order, $forceSyncMode);
    }
}