<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ewave\Digi\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Braintree\Gateway\Helper\SubjectReader;
use Magento\Sales\Model\Order\Item as OrderItem;

/**
 * Class AddressDataBuilder
 */
class CustomFieldsDataBuilder implements BuilderInterface
{
    /**
     * LineItems block name
     */
    const LINE_CUSTOM_FIELDS = 'customFields';

    /**
     * Max length
     */
    const MAX_LENGTH = 254;

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
            foreach ($allItems as $key => $item) {
                if ($item instanceof OrderItem) {
                    $productKey = $this->generateKey($key);
                    $itemsBuildResult[$productKey] = $this->prepareName($item->getName());
                }
            }
            $result[self::LINE_CUSTOM_FIELDS] = $itemsBuildResult;
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
            $result = substr($name, 0, self::MAX_LENGTH);
        }
        return $result;
    }

    /**
     * @param string $key
     * @return string
     */
    public function generateKey($key)
    {
        $result = 'fraud_product_';
        if ($key === null) {
            return sprintf('%s%s', $result, rand(1, 12));
        }
        $key++;
        return sprintf('%s%s', $result, $key);
    }
}
