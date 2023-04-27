<?php

namespace Digidirect\Pronto\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
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
use Magento\Framework\App\Filesystem\DirectoryList;

class OrderFile extends AbstractHelper
{


    protected $_orderCollectionFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;


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

    /**
     * @var LoggerInterface
     */
    protected $logger;

    private $timezone;

    /**
     * @var Country
     */
    public $countryFactory;

    protected $productDigiprot;

    protected $directory;

    /**
     * @var \Magento\GiftMessage\Api\OrderRepositoryInterface
     */
    protected $giftMessageOrderRepository;
    public function __construct(
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
        \Digidirect\CustomOrderLog\Logger\Logger $logger,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        CountryFactory $countryFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\GiftMessage\Api\OrderRepositoryInterface $giftMessageOrderRepository)
    {
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
        $this->logger = $logger;
        $this->timezone = $timezone;
        $this->countryFactory = $countryFactory;
        $this->productFactory = $productFactory;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->giftMessageOrderRepository = $giftMessageOrderRepository;

    }


    public function getOrderCollection()
    {
//        $collection = $this->_orderCollectionFactory->create()
//            ->addAttributeToSelect('*')
//            ->addFieldToFilter('entity_id', array('gteq' => 1077378)) //615813
//            ->setOrder('created_at', 'desc');

        $collection = $this->_orderCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('status',array('eq' => 'complete'))
            ->addFieldToFilter('entity_id', array('gteq' => 1505857
            ));
        $collection->setPageSize(5000); // fetching only x records

        return $collection;
    }



    public function getOrderFile()
    {

        $filepath = 'export/salesorder_3_62.csv';
        $this->directory->create('export');
        $stream = $this->directory->openFile($filepath, 'w+');
        $stream->lock();
        $header = ['Id', 'CustomerId', 'OrderItems', 'CustomerIsGuest', 'CustomerComment',
            'NotifyCustomer','State','Status','TotalQuantityOrdered','TotalDue','GrandTotal',
            'Subtotal','SubtotalIncludingTax','TaxAmount','DiscountTaxCompensationAmount','DiscountAmount',
            'TotalItemCount', 'Weight', 'IncrementId', 'IsVirtual', 'EmailSent', 'ProtectCode',
            'CartId', 'StoreId', 'StoreName','StoreToBaseRate','StoreToOrderRate','HoldBeforeState',
            'HoldBeforeStatus','StoreCurrencyCode','OrderCurrencyCode','GlobalCurrencyCode','BaseCurrencyCode',
            'BaseDiscountAmount','BaseGrandTotal','BaseDiscountTaxCompensationAmount','BaseShippingAmount',
            'BaseShippingDiscountAmount','BaseShippingIncludingTax','BaseShippingTaxAmount','BaseSubtotal','BaseSubtotalIncludingTax',
            'BaseTaxAmount','BaseTotalDue','BaseToGlobalRate','BaseToOrderRate','ShippingAmount','ShippingDescription',
            'ShippingDiscountAmount','ShippingDiscountTaxCompensationAmount','ShippingIncludingTax','ShippingTaxAmount',
            'ShippingAddressPrefix','ShippingAddressFirstName','ShippingAddressMiddleName','ShippingAddressLastName',
            'ShippingAddressSuffix','ShippingAddressCompany','ShippingAddressCountryId','ShippingAddressRegionCode',
            'ShippingAddressRegion','ShippingAddressCity','ShippingAddressStreet','ShippingAddressPhone','ShippingAddressPostcode',
            'ShippingAddressEmail','ShippingAddressVATNumber','BillingAddressPrefix','BillingAddressFirstName','BillingAddressMiddleName',
            'BillingAddressLastName','BillingAddressSuffix','BillingAddressCompany','BillingAddressCountryId','BillingAddressRegionCode',
            'BillingAddressRegion','BillingAddressCity','BillingAddressStreet','BillingAddressPhone','BillingAddressPostcode',
            'BillingAddressEmail','BillingAddressVATNumber','PaymentMethod','PaymentInformation','PaymentAccountStatus','GiftMessageSender',
            'GiftMessageRecipient','GiftMessage','created_at','updated_at','days_to_ship_last_order','QFF'];
        $stream->writeCsv($header);

        //get order data
        $orders = $this->getOrderCollection();
        $counter = 0;
        foreach ($orders as $order)
        {
            if ($order->getState() == 'canceled') {
                continue;
            }
            $counter++;
            $sku = "";
            foreach ($order->getAllVisibleItems() as $item) {
                $sku .= $item->getSku() .",";
            }
            $sku = rtrim($sku, ",");
            $data = [];
            $data[] = $order->getId();
            $data[] = $order->getCustomerId();
            $data[] = $sku;
            $data[] = $order->getCustomerIsGuest();
            $data[] = $order->getCustomerComment();
            $data[] = $order->getNotifyCustomer();
            $data[] = $order->getState();
            $data[] = $order->getStatus();
            $data[] = $order->getTotalQuantityOrdered();
            $data[] = $order->getTotalDue();
            $data[] = $order->getGrandTotal();
            $data[] = $order->getSubtotal();
            $data[] = $order->getSubtotalIncludingTax();
            $data[] = $order->getTaxAmount();
            $data[] = $order->getDiscountTaxCompensationAmount();
            $data[] = $order->getDiscountAmount();
            $data[] = $order->getTotalItemCount();
            $data[] = $order->getWeight();
            $data[] = $order->getIncrementId();
            $data[] = $order->getIsVirtual();
            $data[] = $order->getEmailSent();
            $data[] = $order->getProtectCode();
            $data[] = $order->getCartId();
            $data[] = $order->getStoreId();
            $data[] = $order->getStoreName();
            $data[] = $order->getStoreToBaseRate();
            $data[] = $order->getStoreToOrderRate();
            $data[] = $order->getHoldBeforeState();
            $data[] = $order->getHoldBeforeStatus();
            $data[] = $order->getStoreCurrencyCode();
            $data[] = $order->getOrderCurrencyCode();
            $data[] = $order->getGlobalCurrencyCode();
            $data[] = $order->getBaseCurrencyCode();
            $data[] = $order->getBaseDiscountAmount();
            $data[] = $order->getBaseGrandTotal();
            $data[] = $order->getBaseDiscountTaxCompensationAmount();
            $data[] = $order->getBaseShippingAmount();
            $data[] = $order->getBaseShippingDiscountAmount();
            $data[] = $order->getBaseShippingIncludingTax();
            $data[] = $order->getBaseShippingTaxAmount();
            $data[] = $order->getBaseSubtotal();
            $data[] = $order->getBaseSubtotalIncludingTax();
            $data[] = $order->getBaseTaxAmount();
            $data[] = $order->getBaseTotalDue();
            $data[] = $order->getBaseToGlobalRate();
            $data[] = $order->getBaseToOrderRate();
            $data[] = $order->getShippingAmount();
            $data[] = $order->getShippingDescription();
            $data[] = $order->getShippingDiscountAmount();
            $data[] = $order->getShippingDiscountTaxCompensationAmount();
            $data[] = $order->getShippingIncludingTax();
            $data[] = $order->getShippingTaxAmount();

            $shipaddress = $order->getShippingAddress();
            if(is_null($shipaddress))
            {
                $data[] = "NULL";//$shipaddress->getPrefix();
                $data[] = "NULL";
                $data[] = "NULL";
                $data[] = "NULL";
                $data[] = "NULL";
                $data[] = "NULL";
                $data[] = "NULL";
                $data[] = "NULL";
                $data[] = "NULL";
                $data[] = "NULL";
                $data[] = "NULL";
                $data[] = "NULL";
                $data[] = "NULL";
                $data[] = "NULL";
            }
            else {
                $strt = $shipaddress->getStreet();
                if(is_array($strt))
                {
                    $street = implode(",", $strt);
                }
                else
                {
                    $street = $strt;
                }

                $data[] = $shipaddress->getPrefix();
                $data[] = $shipaddress->getFirstName();
                $data[] = $shipaddress->getMiddleName();
                $data[] = $shipaddress->getLastName();
                $data[] = $shipaddress->getSuffix();
                $data[] = $shipaddress->getCompany();
                $data[] = $shipaddress->getCountryId();
                $data[] = $shipaddress->getRegionCode();
                $data[] = $shipaddress->getRegion();
                $data[] = $shipaddress->getCity();
                $data[] = $street;
                $data[] = $shipaddress->getTelephone();
                $data[] = $shipaddress->getPostcode();
                $data[] = $shipaddress->getEmail();
                $data[] = $shipaddress->getVATNumber();
            }

            $billaddress = $order->getBillingAddress();

            $strtB = $billaddress->getStreet();
            if(is_array($strtB))
            {
                $streetB= implode(",", $strtB);
            }
            else
            {
                $streetB = $strtB;
            }

            $data[] = $billaddress->getPrefix();
            $data[] = $billaddress->getFirstName();
            $data[] = $billaddress->getMiddleName();
            $data[] = $billaddress->getLastName();
            $data[] = $billaddress->getSuffix();
            $data[] = $billaddress->getCompany();
            $data[] = $billaddress->getCountryId();
            $data[] = $billaddress->getRegionCode();
            $data[] = $billaddress->getRegion();
            $data[] = $billaddress->getCity();
            $data[] = $streetB;
            $data[] = $billaddress->getTelephone();
            $data[] = $billaddress->getPostcode();
            $data[] = $billaddress->getEmail();
            $data[] = $billaddress->getVATNumber();

            $paymentInstance = $order->getPayment();

            $data[] = $paymentInstance->getMethod();
            $data[] = $paymentInstance->getInformation();
            $data[] = $paymentInstance->getAccountStatus();

            //$giftMessage = $this->giftMessageOrderRepository->get($order->getId());

            $data[] = "NA";//$giftMessage->getMessageSender();
            $data[] = "NA";//$giftMessage->getMessageRecipient();
            $data[] = "NA";//giftMessage->getMessage();
            $data[] = $order->getCreatedAt();
            $data[] = $order->getUpdateAt();
            $data[] = $order->getDaysToLastShip();
            $data[] = $order->getQffNumber();

            $stream->writeCsv($data);


//            if($counter == 1)
//            {
//                return true; //return after 2 orders
//            }

        }

    }

    public function getTestOrderCollection()
    {

            $collection = $this->_orderCollectionFactory->create()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('status',array('eq' => 'completed'))
                ->addFieldToFilter('entity_id', array('gteq' => 0));
                $collection->setPageSize(5000); // fetching only x records
            return $collection;


    }


}
