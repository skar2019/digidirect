<?php

namespace Ewave\Blog\Block\Widget;

use Ewave\Blog\Model\Config\Provider\Status;
use Ewave\Blog\Model\Post\Source\DisplayType;
use Ewave\Blog\Model\ResourceModel\Post\Collection;
use Magento\Widget\Block\BlockInterface;

class Latest extends AbstractWidget implements BlockInterface
{
    /**
     * @return Collection
     */
    public function getCollection()
    {
        if (!$this->hasData('collection')) {
            $collection = $this->postRepository->getPostList(
                Status::STATUS_ENABLED,
                $this->_storeManager->getStore()->getId(),
                date('Y-m-d')
            );
            $collection->setOrder('publish_date', 'DESC');

            $postsDisplayType = $this->getPostsDisplayType();
            if ($postsDisplayType &&
                $postsDisplayType === DisplayType::SPECIFIED_POSTS_OPTION_VALUE &&
                $postIds = $this->getPostIds()
            ) {
                $selectedPostIds = explode(',', $postIds);
                $collection->addPostIdsFilter($selectedPostIds);
            } else {
                $count = $this->getCount();
                if (!empty($count)) {
                    $collection->setPageSize($count);
                }
            }

            $this->setData('collection', $collection);
            $this->categoryHelper->prepareCategoriesUrls();
        }
        return $this->getData('collection');
    }

    /**
     * Show Post's Category Name option
     * @return mixed
     */
    public function isCategoriesInclude()
    {
        return $this->getShowCategoryName();
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
        $cacheKey['nil'] = $this->getNameInLayout();
        $cacheKey['request_params'] = json_encode($this->getRequest()->getParams());
        $cacheKey['is_show_category_name'] = $this->isCategoriesInclude();
        return $cacheKey;
    }
}
