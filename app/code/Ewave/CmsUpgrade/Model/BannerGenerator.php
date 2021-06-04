<?php
namespace Ewave\CmsUpgrade\Model;

use Magento\Banner\Model\Banner;

/**
 * Class CmsGenerator
 * @package Ewave\CmsUpgrade\Model
 */
class BannerGenerator extends Generator
{
    const NOT_USE_STORE_CONTENTS = 0;

    /**
     * @var DataObject
     */
    protected $_bannerData;

    /**
     * @var Banner
     */
    protected $_banner;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_storeManager;

    /**
     * BannerGenerator constructor.
     * @param GeneratorContext $context
     * @param Banner $banner
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Ewave\CmsUpgrade\Model\GeneratorContext $context,
        Banner $banner,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_banner = $banner;
        $this->_storeManager = $storeManager;
    }

    /**
     * @param array $bannerIds
     * @return \Magento\Framework\DataObject
     */
    public function processUpgradeScript(array $bannerIds)
    {
        $data = $this->_getUpgradeData();
        $data = $this->_getBannersData($bannerIds, $data);
        $nextVersion = $this->_getNextModuleVersion();
        $put = $this->putUpgradeFile($data, $nextVersion);
        if ($put) {
            $this->_changeDbVersion($nextVersion);
        }
        return $this->_result;
    }

    /**
     * Get data for banners
     *
     * @param array $bannerIds
     * @param array $data
     * @return array
     */
    protected function _getBannersData(array $bannerIds, array $data)
    {
        foreach ($bannerIds as $bannerId) {
            /** @var Banner $banner */
            $this->_banner->load($bannerId);
            $data['items'][] = $this->_prepareItemsData($this->_banner);
        }
        return $data;
    }

    /**
     * Prepare item data
     *
     * @param Banner $entity
     * @return array
     */
    protected function _prepareItemsData($entity)
    {
        $this->_bannerData = $this->_fillData($entity);

        $this->_processCustomAttributes()
            ->_prepareBannerContent()
            ->_prepareBannerVideo()
            ->_clearBannerData();

        return $this->_bannerData;
    }

    /**
     * Prepare data for save image banners
     *
     * @return $this
     */
    protected function _prepareBannerContent()
    {
        $stores = $this->_storeManager->getStores();
        $storeIds = [self::NOT_USE_STORE_CONTENTS];

        foreach ($stores as $store) {
            $storeIds[$store->getId()] = $store->getId();
        }
        $storeContents =
            isset($this->_bannerData['store_contents'])
                ? $this->_bannerData['store_contents']
                : [];

        if (!empty($storeContents)) {
            $notUsedStores = array_diff_key($storeIds, $storeContents);
            if (!empty($notUsedStores)) {
                $this->_bannerData['store_contents_not_use'] = $notUsedStores;
            }
        }
        return $this;
    }

    /**
     * Clear data for save image banners
     *
     * @return $this
     */
    protected function _clearBannerData()
    {
        unset(
            $this->_bannerData['images_banner'],
            $this->_bannerData['custom_attributes'],
            $this->_bannerData['banner_id']
        );

        return $this;
    }

    /**
     * Prepare data for save video banners
     *
     * @return $this
     */
    protected function _prepareBannerVideo()
    {
        $imagesBanner =
            isset($this->_bannerData['images_banner'])
                ? $this->_bannerData['images_banner']
                : [];
        if (!empty($imagesBanner) && is_array($imagesBanner)) {
            $uploadedVideos = [];
            foreach ($imagesBanner as $item) {
                if (isset($item['video_folder'])) {
                    unset($item['value_id']);
                    $uploadedVideos[$item['video_folder']] = array_merge(
                        $item,
                        [
                            'video_roles' => implode(',', $item['video_roles']),
                            'preview_image' => $item['file'],
                            'path' => $item['video_file'],
                        ]
                    );
                }
            }
            if (!empty($uploadedVideos)) {
                $this->_bannerData['uploaded_videos'] = $uploadedVideos;
            }
        }
        return $this;
    }

    /**
     * @return $this
     */
    protected function _processCustomAttributes()
    {
        $customAttributes =
            isset($this->_bannerData['custom_attributes'])
                ? $this->_bannerData['custom_attributes']
                : [];

        if (!empty($customAttributes) && is_array($customAttributes)) {
            foreach ($customAttributes as $name => $value) {

                if ('target_id' === $name) {
                    $targetType = $customAttributes['target_type'];
                    if ('product' === $targetType) {
                        $value = 'product/' . $value;
                    }
                    if ($value) {
                        $this->_bannerData[$targetType] = $this->_bannerData[$targetType . '_id'] = $value;
                    }
                }
                $this->_bannerData[$name] = $value;
            }
        }
        return $this;
    }
}