<?php

namespace Ewave\Banner\Model\ResourceModel;

use Ewave\Banner\Helper\IssetTrait;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Ewave\Banner\Preference\Magento\Banner\Model\Banner\Data as BannerData;
use Magento\Banner\Model\Banner as BannerModel;

class Attributes extends AbstractDb
{
    use IssetTrait;

    /**
     * Application Cache Manager
     *
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $cacheManager;

    /**
     * @var array
     */
    protected $bannerAttributesByBannerId = [];

    /**
     * Set table to work with
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ewave_banner_attributes', 'entity_id');
    }

    /**
     * {@inheritdoc}
     */
    public function __construct(
        Context $context,
        CacheInterface $cacheManager,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->cacheManager = $cacheManager;
    }

    /**
     * @param BannerModel $banner
     * @return $this
     */
    public function saveAttributes(BannerModel $banner)
    {
        $attributes = $banner->getData('attributes');
        if ($attributes) {
            $attributes['banner_id'] = $banner->getId();
            $attributesOriginal = $this->selectBannerAttributes($banner->getId());
            if (isset($attributesOriginal['banner_id'])) {
                $this->getConnection()->update(
                    $this->getMainTable(),
                    $attributes,
                    $this->getConnection()->quoteInto('banner_id =?', $banner->getId())
                );
            } else {
                $attributes['banner_id'] = $banner->getId();
                $this->getConnection()->insert(
                    $this->getMainTable(),
                    $attributes
                );
            }
        }
        $this->cacheManager->remove(BannerData::BANNERS_CACHE_KEY);
        return $this;
    }

    /**
     * @param BannerModel $banner
     * @return []
     */
    public function getBannerAttributes(BannerModel $banner)
    {
        return $this->selectBannerAttributes($banner->getId());
    }

    /**
     * @param int $bannerId
     * @param array $fields
     * @return array
     * @deprecated Use method without _
     */
    protected function _selectBannerAttributes($bannerId, $fields = ['*'])
    {
        return $this->selectBannerAttributes($bannerId, $fields);
    }

    /**
     * @param int $bannerId
     * @param array $fields
     * @return array
     */
    protected function selectBannerAttributes($bannerId, $fields = ['*'])
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable(), $fields)
            ->where($this->getConnection()->quoteInto('banner_id = ?', $bannerId));
        return $this->getConnection()->fetchRow($select);
    }

    /**
     * @param int $bannerId
     * @param array $attributes
     * @return array
     */
    public function getBannerAttributesById($bannerId, $attributes = ['*'])
    {
        return $this->selectBannerAttributes($bannerId, $attributes);
    }

    /**
     * @param int $bannerId
     * @param array $fields
     * @return array
     */
    public function getBannersAttributes($bannerId, $fields = ['*'])
    {
        if (isset($this->bannerAttributesByBannerId[$bannerId])) {
            return $this->bannerAttributesByBannerId[$bannerId];
        }
        $select = $this->getConnection()->select()
            ->from($this->getMainTable(), $fields);

        $attributes = $this->getConnection()->fetchAll($select);
        if (!empty($attributes)) {
            foreach ($attributes as $attribute) {
                $bannerIdFromSelect = $this->getByKey($attribute, 'banner_id', null);
                $this->bannerAttributesByBannerId[$bannerIdFromSelect] = $attribute;
            }
        }
        return $this->getByKey($this->bannerAttributesByBannerId, $bannerId, []);
    }
}
