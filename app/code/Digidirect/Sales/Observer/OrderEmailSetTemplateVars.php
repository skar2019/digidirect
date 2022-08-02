<?php

namespace Digidirect\Sales\Observer;

use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Container\OrderIdentity;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Framework\DataObject;

class OrderEmailSetTemplateVars extends \Magento\Sales\Model\Order\Email\Sender implements ObserverInterface 
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    
    /**
     * @var Template
     */
    protected $templateContainer;

    
    /**
     * @var PaymentHelper
     */
    protected $paymentHelper;

    /**
     * @var OrderResource
     */
    protected $orderResource;

    /**
     * Global configuration storage.
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $globalConfig;

    /**
     * @var Renderer
     */
    protected $addressRenderer;

    /**
     * Application Event Dispatcher
     *
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @param Template $templateContainer
     * @param OrderIdentity $identityContainer
     * @param Order\Email\SenderBuilderFactory $senderBuilderFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param Renderer $addressRenderer
     * @param PaymentHelper $paymentHelper
     * @param OrderResource $orderResource
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $globalConfig
     * @param ManagerInterface $eventManager
     */
    public function __construct(
        Template $templateContainer,
        OrderIdentity $identityContainer,
        \Magento\Sales\Model\Order\Email\SenderBuilderFactory $senderBuilderFactory,
        \Psr\Log\LoggerInterface $logger,
        Renderer $addressRenderer,
        PaymentHelper $paymentHelper,
        OrderResource $orderResource,
        \Magento\Framework\App\Config\ScopeConfigInterface $globalConfig
    ) {
        parent::__construct($templateContainer, $identityContainer, $senderBuilderFactory, $logger, $addressRenderer);
        $this->paymentHelper = $paymentHelper;
        $this->orderResource = $orderResource;
        $this->globalConfig = $globalConfig;
        $this->addressRenderer = $addressRenderer;
        $this->identityContainer = $identityContainer;
        $this->logger = $logger ?: ObjectManager::getInstance()->get(LoggerInterface::class);
        $this->templateContainer = $templateContainer;
    }
    

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->logger->debug('Observer '.__CLASS__.' called.');
        $transportObject = $observer->getEvent()->getTransport();
        
        // $transportObject = $observer->getEvent()->getTransportObject();
        $order = $transportObject->getOrder(); 
        $this->logger->debug('Order Id: ' . $order->getId());
        
        // you can use get this var in your order email template
        $transportObject->setData('test_var','Test Value'); // or $transportObject->getTestVar('Test Value');
        
        // get order's shipping method and set the email template based on that
        $shippingMethod = $order->getShippingMethod();
        $this->logger->debug('Order Shipping Method  ' . $shippingMethod);
        
        if ($this->templateContainer) {
            
            $this->templateContainer->setTemplateOptions($this->getTemplateOptions());

            if ($order->getCustomerIsGuest()) {
                $templateId = $this->identityContainer->getGuestTemplateId();
                $customerName = $order->getBillingAddress()->getName();
            } else {
                //$templateId = $this->identityContainer->getTemplateId();
                $templateId = 15;
                $customerName = $order->getCustomerName();
            }
            
            if ($shippingMethod == 'freeshipping_freeshipping') {
                $templateId = 10; // set your template id here based on the shipping method
            }
            
            $this->templateContainer->setTemplateId($templateId);
            
            $orderEmailTemplateId = $this->templateContainer->getTemplateId();
            
            $this->logger->debug('set order email template id based on shipping method : ' . $orderEmailTemplateId);
        }
        
        
    }
    
}