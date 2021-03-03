<?php
namespace Digidirect\AbstractAttributesNavigation\Model\Config\Source;

use Digidirect\AbstractAttributes\Api\AbstractAttributeRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AbstractAttributesCollectionFactory;
/**
 * Class AbstractAttributes
 * @package Digidirect\AbstractAttributesNavigation\Model\Config\Source
 */
class AbstractAttributes implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var AbstractAttributesCollectionFactory
     */
    protected $abstractAttributesRepository;

    /**
     * AbstractAttributes constructor.
     * @param AbstractAttributeRepositoryInterface $attributeRepository
     */
    public function __construct(AbstractAttributeRepositoryInterface $attributeRepository)
    {
        $this->abstractAttributesRepository = $attributeRepository;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $attributesCollection = $this->abstractAttributesRepository->getAbstractAttributes(true);

        $array = [];
        foreach ($attributesCollection as $key => $attribute) {
            /**
             * @var $attribute \Digidirect\AbstractAttributes\Model\AbstractAttribute
             */

            $array[$key] = [
                'value' => $attribute->getAttributeId(),
                'label' => $attribute->getAttributeLabel()
            ];
        }
        return $array;
    }
}
