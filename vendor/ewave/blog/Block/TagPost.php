<?php

namespace Ewave\Blog\Block;

use Ewave\Blog\Api\Data\TagInterface;
use Ewave\Blog\Model\Tag;
use Magento\Framework\DataObject\IdentityInterface;

class TagPost extends Blog implements IdentityInterface
{
    /**
     * @return \Ewave\Blog\Model\ResourceModel\Post\Collection
     */
    protected function prepareCollection()
    {
        $collection = parent::prepareCollection();
        $collection->addFilterByTagId($this->getTag()->getId());
        $this->arrowHelper->setTagCondition($this->getTag()->getId());
        return $collection;
    }

    /**
     * @return Tag
     */
    public function getTag()
    {
        return $this->registry->registry(TagInterface::CURRENT_ITEM);
    }

    /**
     * @return bool
     */
    public function isShowCategoryNamesSetting()
    {
        return (bool)$this->dataHelper->getDisplaySettingsConfig('cat_page/show_category_name_oncat');
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return $this->getTag()->getIdentities();
    }

    /**
     * @return bool|int|null
     */
    public function getCacheLifetime()
    {
        $cacheLifetime = parent::getCacheLifetime();
        if (!$cacheLifetime) {
            $cacheLifetime = 86400;
        }

        return $cacheLifetime;
    }

    /**
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $cacheKey = parent::getCacheKeyInfo();
        $cacheKey['current_item'] = $this->getTag() ? $this->getTag()->getUrl() : null;
        $cacheKey['nil'] = $this->getNameInLayout();
        $cacheKey['request_params'] = json_encode($this->getRequest()->getParams());
        return $cacheKey;
    }
}
