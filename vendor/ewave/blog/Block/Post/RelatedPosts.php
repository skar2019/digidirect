<?php

namespace Ewave\Blog\Block\Post;

use Ewave\Blog\Api\CommentRepositoryInterface;
use Ewave\Blog\Api\Data\PostInterface;
use Ewave\Blog\Api\PostRepositoryInterface;
use Ewave\Blog\Block\Blog;
use Ewave\Blog\Helper\Data;
use Ewave\Blog\Model\Config\Provider\Status;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\Cms\Model\Template\FilterProvider;

/**
 * Class RelatedPosts
 */
class RelatedPosts extends Blog
{
    /**
     * @return bool
     */
    public function isDisplayPosts()
    {
        return (bool)$this->dataHelper->getRelatedSettingsConfig('related_posts/enabled');
    }

    /**
     * @return \Ewave\Blog\Model\ResourceModel\Post\Collection
     */
    public function getCollection()
    {
        if (!$this->hasData('collection')) {
            $collection = $this->postRepository->getPostList(
                Status::STATUS_ENABLED,
                $this->_storeManager->getStore()->getId()
            );
            $collection->setPageSize($this->dataHelper->getRelatedSettingsConfig('related_posts/number_of_posts'));
            $collection->addFilterByRelatedPost($this->getPostId());
            $collection->addOrder('rel.position', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);
            if ($this->getData('show_category_name')
                && $this->dataHelper->getDisplaySettingsConfig('list_page/show_category_name')
            ) {
                $this->categoryHelper->prepareCategoriesUrls();
            }
            $this->setData('collection', $collection);
        }

        return $this->getData('collection');
    }

    /**
     * @return int
     */
    public function getPostId()
    {
        return $this->registry->registry(PostInterface::CURRENT_ITEM)->getId();
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
        return $cacheKey;
    }
}
