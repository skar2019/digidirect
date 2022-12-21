<?php

namespace Digidirect\InvoiceEmail\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;
use Digidirect\AbstractEntity\Model\AbstractEntityRepository;
use Digidirect\InvoiceIncrementId\Model\IncrementIdUpdater;
use Magento\Directory\Model\Country;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class InvoiceEmail extends AbstractHelper
{

    /**
    * @var Curl
    */
    protected $curl;

    protected $_orderCollectionFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var array
     */
    protected $relocateWarehouseMap = [
        'MELB' => 'SWHS',
        'CANN' => 'SWHS',
        'SWHS' => 'MELB'
    ];

    /**
     * @var array
     */
    protected $repCodeForPickUp = [
        '11' => 'S7P',
        '31' => 'B4P',
        '17' => 'M1P',
        '21' => 'M6P',
        '23' => 'C3P',
        '1'  => 'S7P',
        '4'  => 'B4P',
        '7'  => 'M1P',
        '10' => 'B5P',
        '13' => 'M6P',
        '16' => 'C3P',
        '19' => 'B5P',
        '32' => 'P4P',
        '35' => 'C9W'

    ];

    /**
     * @var array
     */
    protected $warehouseCode = [];

    /**
     * @var SourceItemRepositoryInterface
     */
    protected $sourceItemRepository;

    /**
     * @var AbstractEntityRepository
     */
    protected $abstractEntityRepository;

    /**
     * @var IncrementIdUpdater
     */
    protected $incrementIdUpdater;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var CustomerInterface[]|array
     */
    protected $customer = [];

    private $timezone;

    /**
     * @var Country
     */
    public $countryFactory;
    
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


    public function __construct(
        Curl $curl,
        JsonSerializer $jsonSerializer,
        \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $sourceItemsBySku,
        \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemsSaveInterface,
        \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItemFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SourceItemRepositoryInterface $sourceItemRepository,
        AbstractEntityRepository $abstractEntityRepository,
        IncrementIdUpdater $incrementIdUpdater,
        CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        CountryFactory $countryFactory,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger)
    {
        $this->curl = $curl;
        $this->jsonSerializer = $jsonSerializer;
        $this->sourceItemsBySku = $sourceItemsBySku;
        $this->sourceItemsSaveInterface = $sourceItemsSaveInterface;
        $this->sourceItemFactory = $sourceItemFactory;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sourceItemRepository = $sourceItemRepository;
        $this->abstractEntityRepository = $abstractEntityRepository;
        $this->incrementIdUpdater = $incrementIdUpdater;
        $this->customerRepository = $customerRepository;
        $this->timezone = $timezone;
        $this->countryFactory = $countryFactory;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->logger = $logger;

    }

    public function sendInvoiceEmail($test) {

        $orders = $this->getOrderCollection();
        $counter = 0;

        foreach ($orders as $order)
        {
            if($test)
            {
                echo "orders <br>";
            }
            
            $customerFirstName = $order->getCustomerFirstname();
            $customerFullName = $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
            $billingAddress = "vergara St. Tumana, Marikina";
            // $billingAddress = $_order->getBillingAddress();
            $shippingAddress = $_order->getShippingAddress();

            $strt = $address->getStreet();
            $city = $address->getCity();
            $region = $address->getRegion();
            $postcode = $address->getPostcode();
            $countrycode = $address->getCountryId();

            $billingAddress2 = $strt . $city .  $region . $postcode .   $countrycode;
            
            $orderNumber = $order->getIncrementId();
            if($test)
            {
                echo "order -" .$orderNumber." to ".$customerEmail." <br>";
            }
            
            $store = $this->storeManager->getStore();

            $templateParams = ['store' => $store, 'order_number' => $orderNumber, 'customer_firstname' => $customerFirstName, 'customer_fullname' => $customerFullName,'billingAddress' => $billingAddress, 'shippingAddress' => $shippingAddress, 'billingAddress2' => $billingAddress2,];

            $transport = $this->transportBuilder->setTemplateIdentifier(
                'digidirect_invoice_email_template'
                )->setTemplateOptions(
                    ['area' => 'frontend', 'store' => $store->getId()]
                )->addTo(
                    $customerEmail, $customerFirstName
                )->setTemplateVars(
                    $templateParams
                )->setFrom(
                    'general'
                )->addBcc(
                    'jireh@kayweb.com.au', 'rondel@kayweb.com.au'    
                )->getTransport();

            try {
                $transport->sendMessage();
            } catch (\Exception $e) {
                if($test)
                {
                    echo $e->getMessage()."<br>";
                }
                $this->logger->critical($e->getMessage());
                
            }
            
            $order->setData('invoice_email', 1);
            $order->save();
            
        }
        return true;
    }

    public function getOrderCollection()
    {
        
        $collection = $this->_orderCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id', array('gt' => 1172881))
            ->addFieldToFilter('store_id', array('eq' => 1))
            ->addFieldToFilter('status', array('eq' => 'pending'))
            ->addFieldToFilter('invoice_email', array('eq' => 0))
            ->addFieldToFilter('shipping_description', array('neq' =>'Pick Up in Store - Click and Collect Shipping'))
            ->setOrder('created_at', 'asc');

        return $collection;

    }

    
}
