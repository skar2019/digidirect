<?php
namespace Digidirect\AbstractAttributesNavigation\Model\Menu\Type;

use Digidirect\AbstractAttributes\Api\Data\AbstractAttributeInterface;
use Digidirect\Navigation\Model\Menu\Type\AbstractType;
use Digidirect\Navigation\Model\Menu\Type\MenuDataInterface;
use Digidirect\Navigation\Helper\Data as MenuHelper;
use Magento\Framework\Registry;
use Digidirect\AbstractAttributes\Api\AbstractAttributeRepositoryInterface;
use Digidirect\AbstractAttributes\Api\OptionRepositoryInterface;
use Digidirect\AbstractAttributes\Helper\Attribute as AbstractAttributeHelper;

/**
 * Class AbstractAttribute
 *
 * @package Digidirect\AbstractAttributesNavigation\Model\Menu
 */
class AbstractAttribute extends AbstractType implements MenuDataInterface
{
    const PREFIX = 'attribute';
    const ABSTRACT_ATTRIBUTES_REGISTRY_KEY = 'digidirect_abstract_attributes';
    const ABSTRACT_ATTRIBUTES_OPTIONS_REGISTRY_KEY = 'digidirect_abstract_attributes_options';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var AbstractAttributeRepositoryInterface
     */
    protected $abstractAttributeRepository;

    /**
     * @var OptionRepositoryInterface
     */
    protected $optionRepository;

    /**
     * AbstractAttribute constructor.
     *
     * @param MenuHelper $helper
     * @param Registry $registry
     * @param AbstractAttributeRepositoryInterface $abstractAttributeRepository
     * @param OptionRepositoryInterface $optionRepository
     */
    public function __construct(
        MenuHelper $helper,
        Registry $registry,
        AbstractAttributeRepositoryInterface $abstractAttributeRepository,
        OptionRepositoryInterface $optionRepository
    ) {
        parent::__construct($helper);
        $this->registry = $registry;
        $this->abstractAttributeRepository = $abstractAttributeRepository;
        $this->optionRepository = $optionRepository;
    }

    /**
     * @return null|string
     */
    public function getUrl()
    {
        $attribute = $this->getAbstractAttributeByAttributeId($this->item->getAttributeId());
        return $attribute ? $attribute->getUrl() : null;
    }

    /**
     * @return []
     */
    public function getMenuData()
    {
        $array = [
            'is_link' => true,
        ];

        $optionIdsArray = explode(',', $this->item->getOptionIds());
        if (!empty($optionIdsArray)) {
            $attributeId = $this->item->getAttributeId();
            $attribute = $this->getAbstractAttributeByAttributeId($attributeId);
            if ($attribute) {
                $menuId = $this->item->getId();
                $parent = [$attributeId => $menuId];
                $menuByIdArray = [
                    $menuId => [
                        'children' => [],
                    ],
                ];

                foreach ($optionIdsArray as $optionId) {
                    $option = $this->getOption($optionId, $attributeId);
                    if (!$option) {
                        continue;
                    }

                    if (!$option->getStatus()) {
                        continue;
                    }

                    $parentItemId = $parent[$attributeId] ?? self::PREFIX . $attributeId;
                    if (!isset($menuByIdArray[$parentItemId])) {
                        continue;
                    }

                    foreach ([self::PREFIX . $optionId, $parentItemId] as $optionItemId) {
                        if (!isset($menuByIdArray[$optionItemId])) {
                            $menuByIdArray[$optionItemId] = ['value' => $optionItemId];
                        }
                    }

                    $menuByIdArray[self::PREFIX . $optionId] = $this->getAbstractAttributeMenuData($option);
                    $menuByIdArray[$parentItemId]['children'][] = &$menuByIdArray[self::PREFIX . $optionId];
                }

                $menuArray = $menuByIdArray[$menuId]['children'];
                usort(
                    $menuArray,
                    // @codingStandardsIgnoreStart
                    /**
                     * @param array $a
                     * @param array $b
                     * @return int
                     */
                    // @codingStandardsIgnoreEnd
                    function ($a, $b) {
                        if (!isset($a['position']) || !isset($b['position'])) {
                            return 0;
                        }
                        $aPosition = $a['position'];
                        $bPosition = $b['position'];
                        if ($aPosition > $bPosition) {
                            return 1;
                        } elseif ($aPosition < $bPosition) {
                            return -1;
                        } else {
                            return 0;
                        }
                    }
                );

                $array['children'] = $menuArray;
            }
        }

        return $array;
    }

    /**
     * @param \Digidirect\AbstractAttributes\Model\Option $option
     * @return array
     */
    public function getAbstractAttributeMenuData($option)
    {
        $optionMenuItem = [
            'url' => $option->getUrl(),
            'position' => $option->getSortOrder(),
            'title' => $option->getLabel(),
            'identifier' => 'menu-node-option' . $option->getOptionId(),
            'is_link' => true,
            'is_active' => $this->isActiveUrl($option),
            'custom_options' => [],
        ];

        return $optionMenuItem;
    }

    /**
     * @param \Digidirect\AbstractAttributes\Model\Option $option
     * @return bool
     */
    protected function isActiveUrl($option)
    {
        $optionRegistry = $this->registry->registry('current_eaa_option');
        if (!($optionRegistry instanceof \Digidirect\AbstractAttributes\Model\Option)) {
            return false;
        }
        return $option->getOptionId() == $optionRegistry->getOptionId();
    }

    /**
     * @return bool
     */
    public function isAvailable()
    {
        $attribute = $this->getAbstractAttributeByAttributeId($this->item->getAttributeId());
        return $attribute ? $attribute->getStatus() == AbstractAttributeHelper::STATUS_ENABLED
            && $attribute->getListingEnabled() : false;
    }

    /**
     * @return []
     */
    protected function getAbstractAttributes()
    {
        if (!$this->registry->registry(self::ABSTRACT_ATTRIBUTES_REGISTRY_KEY)) {
            $this->registry->unregister(self::ABSTRACT_ATTRIBUTES_REGISTRY_KEY);
            $abstractAttributesList = $this->abstractAttributeRepository->getAbstractAttributes(
                AbstractAttributeHelper::STATUS_ENABLED,
                $this->helper->getCurrentStoreId()
            );
            $abstractAttributes = [];

            foreach ($abstractAttributesList as $abstractAttribute) {
                $abstractAttributes[$abstractAttribute->getAttributeId()] = $abstractAttribute;
            }
            $this->registry->register(self::ABSTRACT_ATTRIBUTES_REGISTRY_KEY, $abstractAttributes);
        }

        return $this->registry->registry(self::ABSTRACT_ATTRIBUTES_REGISTRY_KEY);
    }

    /**
     * @param int $abstractAttributeId
     * @return AbstractAttributeInterface
     */
    protected function getAbstractAttributeByAttributeId($abstractAttributeId)
    {
        $abstractAttributes = $this->getAbstractAttributes();
        return $abstractAttributes[$abstractAttributeId] ?? null;
    }

    /**
     * @param int $attributeId
     * @return \Digidirect\AbstractAttributes\Api\Data\OptionInterface[]
     */
    protected function getAbstractAttributesOptions($attributeId)
    {
        if (!$this->registry->registry(self::ABSTRACT_ATTRIBUTES_OPTIONS_REGISTRY_KEY . '_' . $attributeId)) {
            $this->registry->unregister(self::ABSTRACT_ATTRIBUTES_OPTIONS_REGISTRY_KEY . '_' . $attributeId);
            $optionsFromRepository = $this->optionRepository->getAttributeOptions(
                $attributeId, $this->helper->getCurrentStoreId()
            );
            $this->registry->register(self::ABSTRACT_ATTRIBUTES_OPTIONS_REGISTRY_KEY . '_' . $attributeId,
                $optionsFromRepository);
        }
        return $this->registry->registry(self::ABSTRACT_ATTRIBUTES_OPTIONS_REGISTRY_KEY . '_' . $attributeId);
    }

    /**
     * @param int $optionId
     * @param int $attributeId
     * @return \Digidirect\AbstractAttributes\Api\Data\OptionInterface|null
     */
    protected function getOption($optionId, $attributeId)
    {
        $abstractOptions = $this->getAbstractAttributesOptions($attributeId);
        foreach ($abstractOptions as $option) {
            if ($option->getOptionId() == $optionId) {
                return $option;
            }
        }
        return $abstractOptions[$optionId] ?? null;
    }
}
