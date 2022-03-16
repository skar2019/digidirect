<?php

namespace Marketplacer\Brand\Plugin\Eav\Block\Adminhtml\Attribute\Edit\Options;

use Magento\Eav\Block\Adminhtml\Attribute\Edit\Options\Options as OptionsBlock;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\Registry;
use Marketplacer\Brand\Helper\Config;
use Marketplacer\BrandApi\Api\BrandAttributeRetrieverInterface;

class DisableBrandOptionEdit
{
    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * @var Config
     */
    protected $configHelper;

    /**
     * @var BrandAttributeRetrieverInterface
     */
    protected $brandAttributeRetriever;

    /**
     * @param Registry $registry
     * @param Config $brandConfigHelper
     * @param BrandAttributeRetrieverInterface $brandAttributeRetriever
     */
    public function __construct(
        Registry $registry,
        Config $brandConfigHelper,
        BrandAttributeRetrieverInterface $brandAttributeRetriever
    ) {
        $this->_registry = $registry;
        $this->configHelper = $brandConfigHelper;
        $this->brandAttributeRetriever = $brandAttributeRetriever;
    }

    /**
     * @param OptionsBlock $subject
     * @param string $key
     * @param null $index
     * @param mixed $result
     * @return bool | mixed
     */
    public function afterGetData(OptionsBlock $subject, $result = null, $key = '', $index = null)
    {
        if ('read_only' !== $key) {
            return $result;
        }

        $attribute = $this->getAttribute();

        if ($attribute && $this->brandAttributeRetriever->getAttributeCode() === $attribute->getAttributeCode()) {
            $result = true;
            return $result;
        }

        return $result;
    }

    /**
     * Retrieve attribute object from registry
     *
     * @return AbstractAttribute
     * @codeCoverageIgnore
     * @see \Magento\Eav\Block\Adminhtml\Attribute\Edit\Options\Options::getAttributeObject
     *
     */
    protected function getAttribute()
    {
        return $this->_registry->registry('entity_attribute');
    }
}
