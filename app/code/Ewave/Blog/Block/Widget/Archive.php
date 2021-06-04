<?php
namespace Ewave\Blog\Block\Widget;

use Ewave\Blog\Api\Data\PostInterface;
use Ewave\Blog\Model\Config\Provider\Status;
use Ewave\Blog\Model\ResourceModel\Post\Collection;
use Magento\Framework\View\Element\Template;

class Archive extends AbstractWidget
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
            $collection->setOrder(PostInterface::FIELD_PUBLISH_DATE, 'DESC');
            $this->setData('collection', $collection);
            $count = $this->getCount();
            if (!empty($count)) {
                $collection->setPageSize($count);
            }
        }
        return $this->getData('collection');
    }

    /**
     * @return array
     */
    public function monthList()
    {
        $monthYear = [];
        foreach ($this->getCollection() as $post) {
            $time = strtotime($post->getData(PostInterface::FIELD_PUBLISH_DATE));
            $monthYear[date('Y-m', $time)] = $time;
        }
        return $monthYear;
    }

    /**
     * @param string $time
     * @return mixed
     */
    public function getYear($time)
    {
        return date('Y', $time);
    }

    /**
     * @param string $time
     * @return mixed
     */
    public function getMonth($time)
    {
        return date('F', $time);
    }

    /**
     * @param string $time
     * @return string
     */
    public function getArchiveUrl($time)
    {
        return $this->getUrl('archive/' . date('Y-m', $time));
    }
}
