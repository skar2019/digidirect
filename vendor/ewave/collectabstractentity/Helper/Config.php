<?php
namespace Ewave\CollectAbstractEntity\Helper;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 * @package Ewave\CollectAbstractEntity\Helper
 */
class Config extends AbstractHelper
{
    const XML_PATH_COLLECT_FIELDS_MATRIX = 'carriers/collect/click_collect_fields_matrix';
    const XML_PATH_COLLECT_ABSTRACT_ENTITY = 'carriers/collect/click_collect_entity';

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * Config constructor.
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
        $json = $this->scopeConfig->getValue(
            self::XML_PATH_COLLECT_FIELDS_MATRIX,
            ScopeInterface::SCOPE_WEBSITE,
            $storeId
        );
        $array = $json ? $this->serializer->unserialize($json) : [];

        $map = [];
        foreach ($array as $item) {
            $map[$item['collect_field_column']] = $item['abstract_entity_attribute_field_column'];
        }
        return $map;
    }
}
