<?php

namespace Ewave\Blog\Sql;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Ewave\Blog\Model\CurrentStoreFetcher;
use Ewave\Blog\Api\Data\TagInterface;
use Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * Class TagSave
 * @package Ewave\Blog\Sql
 */
class TagSave extends AbstractDb implements CurrentStoreContentCheckerInterface
{
    /**
     * @var CurrentStoreFetcher
     */
    protected $currentStore;

    /**
     * @var array
     */
    protected $cacheByEntity = [];

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_setMainTable('ewave_blog_post_tags');
    }

    /**
     * CategoryInformationSave constructor.
     *
     * @param Context $context
     * @param CurrentStoreFetcher $currentStoreFetcher
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        CurrentStoreFetcher $currentStoreFetcher,
        $connectionName = null
    ) {
        $this->currentStore = $currentStoreFetcher;
        parent::__construct($context, $connectionName);
    }

    /**
     * @param int $entityId
     * @param int $storeId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function hasStoreViewContent(int $postId, int $storeId): bool
    {
        $cacheKey = $postId . $storeId . $this->getMainTable();
        if (!isset($this->cacheByEntity[$cacheKey])) {

            $select = $this->getConnection()->select()
                ->from($this->getMainTable())
                ->where('store_id = ?', $storeId)
                ->where('post_id = ?', $postId);
            $has = $this->getConnection()->fetchRow($select);

            $this->cacheByEntity[$cacheKey] = !empty($has);
        }

        return $this->cacheByEntity[$cacheKey] ?? false;
    }
}
