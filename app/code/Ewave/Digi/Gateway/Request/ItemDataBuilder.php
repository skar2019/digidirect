<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ewave\Digi\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Braintree\Gateway\SubjectReader;
use Magento\Sales\Model\Order\Item as OrderItem;

/**
 * Class AddressDataBuilder
 */
class ItemDataBuilder implements BuilderInterface
{
    /**
     * LineItems block name
     */
    const LINE_ITEMS = 'lineItems';

    /**
     * The name value must be less than or equal to 255 characters.
     */
    const PRODUCT_NAME = 'name';

    /**
     * The name value must be less than or equal to 255 characters.
     */
    const FULL_NAME_DESCRIPTION = 'description';

    /**
     * Transaction type
     */
    const KIND = 'kind';

    /**
     * Quantity products
     */
    const QUANTITY = 'quantity';

    /**
     * Item Amount
     */
    const ITEM_AMOUNT = 'unitAmount';

    /**
     * Unit of measure
     */
    const UNIT_OF_MEASURE = 'unitOfMeasure';

    /**
     * Total amount
     */
    const TOTAL_AMOUNT = 'totalAmount';

    /**
     * Product code
     */
    const PRODUCT_CODE = 'productCode';

    /**
     * COMMODITY_CODE
     */
    const COMMODITY_CODE = 'commodityCode';

    /**
     * Debit type
     */
    const DEBIT_TYPE = 'debit';

    /**
     * Debit type
     */
    const MEASURE_ITEM = 'item';

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * Constructor
     *
     * @param SubjectReader $subjectReader
     */
    public function __construct(SubjectReader $subjectReader)
    {
        $this->subjectReader = $subjectReader;
    }

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);

        $order = $paymentDO->getOrder();
        $result = [];
        $allItems = $order->getItems();
        if (!empty($allItems)) {
            $itemsBuildResult = [];
            foreach ($allItems as $item) {
                if ($item instanceof OrderItem) {
                    $itemsBuildResult[] = [
                        self::PRODUCT_NAME => $this->prepareName($item->getName()),
                        self::FULL_NAME_DESCRIPTION => $this->prepareDescription($item->getName()),
                        self::KIND => self::DEBIT_TYPE,
                        self::QUANTITY => $item->getQtyOrdered(),
                        self::ITEM_AMOUNT => number_format($item->getPrice(), 2, '.', ''),
                        self::UNIT_OF_MEASURE => self::MEASURE_ITEM,
                        self::TOTAL_AMOUNT => number_format($item->getRowTotal(), 2, '.', ''),
                        self::PRODUCT_CODE => $this->prepareCode($item->getSku()),
                        self::COMMODITY_CODE => $this->prepareCode($item->getSku()),
                    ];
                }
            }
            $result[self::LINE_ITEMS] = $itemsBuildResult;
        }
        return $result;
    }

    /**
     * @param string $name
     * @return string
     */
    public function prepareName($name)
    {
        $result = '';
        if (!empty($name)) {
            $name = preg_replace("/[^a-zA-Z0-9-,.]+/", " ", $name);
            $result = substr($name, 0, 35);
        }
        return $result;
    }

    /**
     * @param string $code
     * @return bool|string
     */
    public function prepareCode($code)
    {
        $result = '';
        if (!empty($code)) {
            $code = preg_replace("/[^a-zA-Z0-9-,.]+/", " ", $code);
            $result = substr($code, 0, 12);
        }
        return $result;
    }

    /**
     * @param string $description
     * @return bool|string
     */
    public function prepareDescription($description)
    {
        $result = '';
        if (!empty($description)) {
            $description = preg_replace("/[^a-zA-Z0-9-,.]+/", " ", $description);
            $result = substr($description, 0, 126);
        }
        return $result;
    }
}
