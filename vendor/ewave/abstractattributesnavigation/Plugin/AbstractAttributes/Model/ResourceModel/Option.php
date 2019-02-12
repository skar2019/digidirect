<?php

namespace Ewave\AbstractAttributesNavigation\Plugin\AbstractAttributes\Model\ResourceModel;

use Ewave\AbstractAttributes\Model\ResourceModel\Option as AbstractAttributeOptionResource;
use Ewave\AbstractAttributes\Model\Option as OptionModel;

/**
 * Class Option
 * @package Ewave\AbstractAttributesNavigation\Plugin\AbstractAttributes\Model\ResourceModel
 */
class Option
{
    /**
     * @var \Ewave\Navigation\Model\Cache
     */
    protected $navigationCache;

    /**
     * @var \Ewave\AbstractAttributesNavigation\Model\ResourceModel\Processor
     */
    protected $resourceModel;

    /**
     * Option constructor.
     * @param \Ewave\Navigation\Model\Cache $navigationCache
     * @param \Ewave\AbstractAttributesNavigation\Model\ResourceModel\Processor $processor
     */
    public function __construct(
        \Ewave\Navigation\Model\Cache $navigationCache,
        \Ewave\AbstractAttributesNavigation\Model\ResourceModel\Processor $processor
    ) {
        $this->navigationCache = $navigationCache;
        $this->resourceModel = $processor;
    }

    /**
     * @param AbstractAttributeOptionResource $optionResource
     * @param \Closure $proceed
     * @param OptionModel $option
     * @return AbstractAttributeOptionResource
     */
    public function aroundSave(
        AbstractAttributeOptionResource $optionResource,
        \Closure $proceed,
        OptionModel $option
    ) {
        $result = $proceed($option);
        if ($option->getAttribute()->hasDataChanges()) {
            $attributeId = $option->getAttributeId();
            $menuItemIds = $this->resourceModel->getMenuIdByTargetEntityId($attributeId);
            if (!empty($menuItemIds) && is_array($menuItemIds)) {
                $cacheTags = [];
                foreach ($menuItemIds as $menuItemId) {
                    $cacheTags[] = \Ewave\Navigation\Model\Menu::CACHE_TAG . '_' . $menuItemId;
                }
                if (!empty($cacheTags)) {
                    $this->navigationCache->execute($cacheTags);
                }
            }
        }
        return $result;
    }
}
