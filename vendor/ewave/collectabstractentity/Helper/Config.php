<?php
namespace Ewave\CollectAbstractEntity\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 *
 * @package Ewave\CollectAbstractEntity\Helper
 */
class Config extends AbstractHelper
{
    const XML_PATH_COLLECT_FIELDS_MATRIX = 'carriers/collect/click_collect_fields_matrix';
    const XML_PATH_COLLECT_ABSTRACT_ENTITY = 'carriers/collect/click_collect_entity';
    const XML_PATH_PREFILL_SHIPPING_ADDRESS = 'carriers/collect/prefill_shipping_address_fields_matrix';

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var array
     */
    protected $collectFields;

    /**
     * @var string[]
     */
    protected $prefillShippingAddressFields;

    /**
     * Config constructor.
     *
     * @param Context $context
     * @param SerializerInterface $serializer
     */
    public function __construct(
        Context $context,
        SerializerInterface $serializer
    ) {
        parent::__construct($context);
        $this->serializer = $serializer;
    }

    /**
     * @param null|string $storeId
     * @return string
     */
    public function getCollectAbstractEntityId($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_COLLECT_ABSTRACT_ENTITY,
            ScopeInterface::SCOPE_WEBSITE,
            $storeId
        );
    }

    /**
     * @param null|string $storeId
     * @return array
     */
    public function getCollectFieldsMatrix($storeId = null)
    {
        if (null === $this->collectFields) {
            $json = $this->scopeConfig->getValue(
                self::XML_PATH_COLLECT_FIELDS_MATRIX,
                ScopeInterface::SCOPE_WEBSITE,
                $storeId
            );
            $array = $json ? $this->serializer->unserialize($json) : [];

            $this->collectFields = [];
            foreach ($array as $item) {
                $this->collectFields[$item['collect_field_column']] = $item['abstract_entity_attribute_field_column'];
            }
        }

        return $this->collectFields;
    }

    /**
     * @param null|string $storeId
     * @return array
     */
    public function getPrefillShippingAddressFieldsMatrix($storeId = null)
    {
        if (null === $this->prefillShippingAddressFields) {
            $json = $this->scopeConfig->getValue(
                self::XML_PATH_PREFILL_SHIPPING_ADDRESS,
                ScopeInterface::SCOPE_WEBSITE,
                $storeId
            );
            $array = $json ? $this->serializer->unserialize($json) : [];

            $this->prefillShippingAddressFields = [];
            foreach ($array as $item) {
                $this->prefillShippingAddressFields[$item['shipping_address_field_column']] =
                    $item['abstract_entity_attribute_field_column'];
            }

        }

        return $this->prefillShippingAddressFields;
    }
}
