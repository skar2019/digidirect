<?php

declare(strict_types=1);

namespace Ewave\Migration\Model;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Magento\Customer\Block\Form\Register;
use Magento\Customer\Model\CustomerFactory;
use \Magento\Customer\Model\Customer;
use Magento\Store\Model\StoreManagerInterface;
use Ewave\Migration\Helper\Profiler;
use Ewave\Migration\Helper\Data;
use Magento\Framework\Filesystem\Driver\File;
use Ewave\AISales\Model\Import\Order\Order as ImportOrder;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

/**
 * Class KaywebOrderMigrationProcessor
 * @package Ewave\Migration\Model
 */
class KaywebOrderMigrationProcessor
{
    const CUSTOMER_EMAIL = 'customer_email';

    const BILLING_ADDRESS  = 'ba';
    const SHIPPING_ADDRESS = 'sa';

    const PRODUCT_NAME = [
        'name',
        'unit_price',
        'quantity',
        'discount',
        'total_price',
        'pronto_code',
        'product_id',
    ];

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var Register
     */
    private $registerBlock;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Profiler
     */
    private $profiler;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var File
     */
    private $file;

    /**
     * @var ImportOrder
     */
    private $orderImport;
    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * KaywebOrderMigrationProcessor constructor.
     * @param CustomerFactory $customerFactory
     * @param Register $registerBlock
     * @param StoreManagerInterface $storeManager
     * @param Profiler $profiler
     * @param Data $helper
     * @param File $file
     * @param ImportOrder $orderImport
     * @param JsonSerializer $jsonSerializer
     */
    public function __construct(
        CustomerFactory $customerFactory,
        Register $registerBlock,
        StoreManagerInterface $storeManager,
        Profiler $profiler,
        Data $helper,
        File $file,
        ImportOrder $orderImport,
        JsonSerializer $jsonSerializer
    ) {
        $this->customerFactory = $customerFactory;
        $this->registerBlock = $registerBlock;
        $this->storeManager = $storeManager;
        $this->profiler = $profiler;
        $this->helper = $helper;
        $this->file = $file;
        $this->orderImport = $orderImport;
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * @param OutputInterface $output
     * @param string $filePath
     * @param bool $debug
     * @return array
     */
    public function process(
        OutputInterface $output,
        string $filePath,
        bool $debug = false
    ) {

        $result = [];
        $q = iterator_count($this->getLines($filePath));
        $progressBar = new ProgressBar($output, $q);
        $progressBar->setFormat($debug ? 'debug' : 'verbose');
        $progressBar->start();

        $fileGenerator = $this->getLines($filePath);
        $nameField = $fileGenerator->current();
        foreach ($fileGenerator as $key => $orderValue) {
            if ($key === 0) {
                continue;
            }
            try {
                $result[] = $this->createOrder(array_combine($nameField, $orderValue));
                $progressBar->advance();

                if ($debug && $key % 500 == 0) {
                    $output->writeln('');
                    $output->writeln($this->profiler->getProcessMemoryUsage());
                }
            } catch (\Exception $e) {
                $output->writeln('');
                $output->writeln(sprintf('Error: %s', $e->getMessage()));
                $output->writeln(sprintf('Customer Data: %s', implode(',', $orderValue)));
            }
        }

        $progressBar->finish();

        return ['all' => $q, 'executed' => count(array_filter($result))];
    }

    /**
     * @param string $filePath
     * @return \Generator
     */
    private function getLines(string $filePath)
    {
        $f = $this->file->fileOpen($filePath, 'r');

        while ($line = $this->file->fileGetCsv($f)) {
            yield $line;
        }
        $this->file->fileClose($f);
    }

    /**
     * @param array $orderData
     * @return bool
     */
    private function createOrder(array $orderData)
    {

        $currentCustomer = $this->getCustomerByEmail($orderData);
        if ($currentCustomer->getId()) {
            $parsedOrderData = [];

            $parsedOrderData['order_items'] = $this->getOrderItems($orderData);
            $parsedOrderData['order'] = $this->getOrder(
                $orderData,
                $currentCustomer,
                count($parsedOrderData['order_items'])
            );
            $parsedOrderData['payment'] = $this->getOrderPayment($orderData);
            $parsedOrderData['billing_address'] = $this->getOrderAddress($orderData, self::BILLING_ADDRESS);
            $parsedOrderData['shipping_address'] = $this->getOrderAddress($orderData, self::SHIPPING_ADDRESS);
            $parsedOrderData['custom_kw_data'] = $this->getCustomKwData($orderData);

            $newOrderData = $this->combineOrderData($parsedOrderData);

            $this->orderImport->saveBunch($newOrderData);

            return true;
        }
        return false;
    }

    /**
     * @param array $orderData
     * @return array
     */
    public function getCustomKwData(array $orderData)
    {
        $customKwDefaultData = [];
        $customKwDefaultData['kw_delivery_notice'] = $orderData['custom_kw_delivery_notice'];
        $customKwDefaultData['kw_billing_notice'] = $orderData['custom_kw_billing_notice'];
        $customKwDefaultData['pronto_order_number'] = $orderData['order_pronto_number'];
        $customKwDefaultData['kw_order_additional_info'] =
           $this->jsonSerializer->serialize([
               'payment_type' => $orderData['custom_kw_payment_type'],
               'current_status' => $orderData['custom_kw_current_status'],
               'zip_order_id' =>$orderData['custom_kw_zip_order_id'],
               'brain_tree_type' => $orderData['custom_kw_brain_tree_type'],
               'coupon_code' => $orderData['custom_kw_coupon_code'],
               'pronto_id_number' => $orderData['order_pronto_number'],
               'order_transaction_id' => $orderData['order_transaction_id']
                ]);
        return $customKwDefaultData;
    }
    /**
     * @param array $orderData
     * @return array
     */
    public function combineOrderData(array $orderData)
    {
        $newOrderData = array_merge($orderData['order'], $orderData['custom_kw_data']);
        $newOrderData['items'] = $orderData['order_items'];
        $newOrderData['payments'] = $orderData['payment'];
        $newOrderData['billing_address'] = $orderData['billing_address'];
        $newOrderData['shipping_address'] = $orderData['shipping_address'];

        return [$newOrderData];
    }
    /**
     * @param array $orderData
     * @return \Magento\Customer\Model\Customer
     */
    private function getCustomerByEmail(array $orderData)
    {
        /**
         * @var $customer \Magento\Customer\Model\Customer
         */
        $customer = $this->customerFactory->create();
        $store = $this->storeManager->getStore();
        $customer->setWebsiteId($store->getWebsiteId());
        if (isset($orderData[self::CUSTOMER_EMAIL])) {
            $customer->loadByEmail($orderData[self::CUSTOMER_EMAIL]);
        }
        return $customer;
    }

    /**
     * @param array $order
     * @return array|mixed
     */
    public function getOrderItems(array $order)
    {
        $productsDefaultData = [$this->getOrderItemsDefault()];
        $productData = explode(',', $order['products']);
        $products = array_reduce($productData, function ($acc, $product) {
            if (!empty($product)) {
                $product = explode(':', $product);
                $diff = count(self::PRODUCT_NAME) - count($product);
                if ($diff != 0) {
                    $ar = array_fill($diff, $diff, ' ');
                    $product = array_merge($product, $ar);
                }
                $acc[] = array_combine(self::PRODUCT_NAME, $product);
            }
            return $acc;
        }, []);

        $result = array_reduce($products, function ($acc, $product) use ($order) {
            $defaultProduct = $this->getOrderItemsDefault();
            $sku = $this->getSku($product);
            $defaultProduct['sku'] = $sku;
            $defaultProduct['name'] = $product['name'];
            $defaultProduct['no_discount'] = empty($product['discount']);
            $defaultProduct['qty_ordered'] = (int)$product['quantity'];
            $defaultProduct['price'] = (float)$product['unit_price'];
            $defaultProduct['base_price'] = (float)$product['unit_price'];
            $defaultProduct['original_price'] = (float)$product['unit_price'];
            $defaultProduct['discount_amount'] = !empty($product['discount']) ? $product['discount'] : 0;
            $defaultProduct['row_total'] = (float)$product['total_price'];
            $defaultProduct['base_row_total'] = (float)$product['total_price'];

            $acc[] = $defaultProduct;
            return $acc;
        }, []);

        return !empty($result) ? $result : $productsDefaultData;
    }

    /**
     * @param array $orderData
     * @param Customer $currentCustomer
     * @param $quantityProduct
     * @return array
     */
    public function getOrder(array $orderData, Customer $currentCustomer, $quantityProduct)
    {
        $baseSubtotal = (float)$orderData['order_total_price'] - (float)$orderData['order_shipping_price'];
        $result = $this->getDefaultOrderEntities();
        $result['increment_id'] = $orderData['increment_id'];
        $result['customer_id'] = $currentCustomer->getId();
        $result['base_grand_total'] = (float)$orderData['order_total_price'];
        $result['base_shipping_amount'] = (float)$orderData['order_shipping_price'];
        $result['base_subtotal'] = $baseSubtotal;
        $result['grand_total'] = (float)$orderData['order_total_price'];
        $result['total_paid'] = (float)$orderData['order_total_price'];
        $result['base_total_paid'] = (float)$orderData['order_total_price'];
        $result['shipping_amount'] = (float)$orderData['order_shipping_price'];
        $result['subtotal'] = $baseSubtotal;
        $result['total_qty_ordered'] = $quantityProduct;
        $result['customer_firstname'] = $currentCustomer->getFirstname();
        $result['customer_lastname'] = $currentCustomer->getLastname();
        $result['customer_group_id'] = $currentCustomer->getGroupId();
        $result['base_subtotal_incl_tax'] = $baseSubtotal;
        $result['subtotal_incl_tax'] = $baseSubtotal;
        $result['customer_email'] = $currentCustomer->getEmail();
        $result['total_item_count'] = $quantityProduct;
        $result['shipping_incl_tax'] = (float)$orderData['order_shipping_price'];
        $result['base_shipping_incl_tax'] = (float)$orderData['order_shipping_price'];

        return $result;
    }

    /**
     * @param array $orderData
     * @return array
     */
    public function getOrderPayment(array $orderData)
    {
        $defaultPayment  = $this->getOrderPaymentEntityDefault();
        $defaultPayment['base_shipping_amount'] = (float)$orderData['order_shipping_price'];
        $defaultPayment['shipping_amount'] = (float)$orderData['order_shipping_price'];
        $defaultPayment['base_amount_ordered'] = (float)$orderData['order_total_price'];
        $defaultPayment['amount_ordered'] = (float)$orderData['order_total_price'];

        return [$defaultPayment];
    }

    /**
     * @param array $orderData
     * @param string $typeAddress
     * @return array
     *
     * * $type address has only two type
     * 1. 'sa' - sippint address
     * 2. 'ba' - billing address
     */
    public function getOrderAddress(array $orderData, string $typeAddress)
    {
        $defaultBillingAddress = $this->getAddressOrderEntityDefault();

        if ($typeAddress && ($typeAddress == 'sa' || $typeAddress == 'ba')) {
            $defaultBillingAddress['firstname'] = $orderData['order_' . $typeAddress . '_first_name'];
            $defaultBillingAddress['lastname'] = $orderData['order_' . $typeAddress . '_last_name'];
            $defaultBillingAddress['postcode'] = $orderData['order_' . $typeAddress . '_postcode'];
            $defaultBillingAddress['street'] = $orderData['order_' . $typeAddress . '_street'];
            $defaultBillingAddress['city'] = $orderData['order_' . $typeAddress . '_city'];
            $defaultBillingAddress['email'] = $orderData['customer_email'];
            $defaultBillingAddress['telephone'] = $orderData['order_' . $typeAddress . '_telephone'];
            $defaultBillingAddress['country_id'] =
                $orderData['order_' . $typeAddress . '_country']
                || !empty($orderData['order_' . $typeAddress . '_country'])
                    ? $orderData['order_' . $typeAddress . '_country'] : 'AU';
            $defaultBillingAddress['region'] = $orderData['order_' . $typeAddress . '_province'];
            $defaultBillingAddress['company'] = $orderData['order_' . $typeAddress . '_province'];
        }
        return $defaultBillingAddress;
    }

    /**
     * @param array $product
     * @return string
     */
    public function getSku(array $product)
    {
        $sku = 'kw' . $product['product_id'];
        if (!empty($product['pronto_code'])) {
            $sku = 'sku_'. $product['pronto_code'];
        }
        return $sku;
    }
    /**
     * @return array
     */
    public function getOrderItemsDefault()
    {
        return [
            'sku' => '24-WB03', //Reguired Identify field
            'name' => 'noProduct',
            'no_discount' => true,
            'qty_canceled' => 0,
            'qty_invoiced' => 0,
            'qty_ordered' => 1,
            'qty_refunded' => 0,
            'qty_shipped' => 0,
            'qty_returned' => 0,
            'price' => 100,
            'base_price' => 100,
            'original_price' => 100,
            'tax_percent' => 0,
            'tax_amount' => 0,
            'base_tax_amount' => 0,
            'tax_invoiced' => 0,
            'base_tax_invoiced' => 0,
            'discount_percent' => 0,
            'discount_amount' => 0,
            'base_discount_amount' => 0,
            'discount_invoiced' => 0,
            'base_discount_invoiced' => 0,
            'amount_refunded' => 0,
            'base_amount_refunded' => 0,
            'row_total' => 100,
            'base_row_total' => 100,
            'row_invoiced' => 0,
            'base_row_invoiced' => 0,
            'weight' => 10,
            'row_weight' => 10,
        ];
    }

    /**
     * @return array
     */
    public function getAddressOrderEntityDefault()
    {
        return [
            'firstname' => 'User',
            'lastname' => 'User',
            'postcode' => '3000',
            'street' => 'Collins Street',
            'city' => 'Melbourne',
            'email' => 'legacy@user.com',
            'telephone' => '0396086990',
            'country_id' => 'AU',
            'region' => 'Victoria',
            'company' => 'digidirect'
        ];
    }
    /**
     * @return array
     */
    public function getOrderPaymentEntityDefault()
    {
        return [
            //You can define "entity_id" field for update
            'base_shipping_amount' => 5,
            'shipping_amount' => 5,
            'base_amount_ordered' => 100,
            'amount_ordered' => 100,
            'method' => 'checkmo',
        ];
    }

    /**
     * @return array
     */
    public function getDefaultOrderEntities()
    {
        return [
            'increment_id' => '99999', //Reguired Identify field
            'customer_id' => 3,
            'store_id' => 1, //Reguired Identify field
            'state' => 'complete',
            'status' => 'complete',
            'is_virtual' => false,
            'base_discount_amount' => 0,
            'base_grand_total' => 1000,
            'base_shipping_amount' => 10,
            'base_shipping_tax_amount' => 0,
            'base_subtotal' => 990,
            'base_tax_amount' => 0,
            'base_to_global_rate' => 1,
            'base_to_order_rate' => 1,
            'discount_amount' => 0,
            'grand_total' => 1000,
            'total_paid' => 1000,
            'base_total_paid' => 1000,
            'shipping_amount' => 10,
            'shipping_tax_amount' => 0,
            'store_to_base_rate' => 1,
            'store_to_order_rate' => 1,
            'subtotal' => 990,
            'tax_amount' => 0,
            'total_qty_ordered' => 2,
            'customer_firstname' => 'Default Name',
            'customer_lastname' => 'Default Last Name',
            'customer_is_guest' => false,
            'customer_note_notify' => true,
            'customer_group_id' => 0,
            'base_shipping_discount_amount' => 0,
            'base_subtotal_incl_tax' => 990,
            'shipping_discount_amount' => 0,
            'subtotal_incl_tax' => 990,
            'weight' => 10,
            'base_currency_code' => 'USD',
            'customer_email' => 'legacy@user.com',
            'global_currency_code' => 'USD',
            'order_currency_code' => 'USD',
            'store_currency_code' => 'USD',
            'total_item_count' => 2,
            'shipping_incl_tax' => 10,
            'base_shipping_incl_tax' => 10,
            'shipping_method' => 'freeshipping_freeshipping',
            'shipping_description' => 'Legacy shipping method',
            'items' => [],
            'payments' => [],
            'billing_address' => [],
            'shipping_address' => [],
            'status_histories' => [
                [
                    //You can define "entity_id" field for update
                    'comment' => 'Order Was paid',
                    'status' => 'processing',
                    'entity_name' => 'order',
                ]
            ]
        ];
    }
}
