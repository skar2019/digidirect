<?php

namespace Ewave\Banner\Controller\Adminhtml\Banner;

use Ewave\Banner\Helper\Image\Config as ConfigHelper;
use Ewave\Banner\Inheritance\Magento\Banner\Controller\Adminhtml\Banner\Save as SaveBannerInheritance;
use Ewave\Banner\Model\Attributes\NavigationImage;
use Ewave\Banner\Model\Cache;

/**
 * Controller is overwritten due images uploading
 * Added clear cache invalidation message
 */
class Save extends SaveBannerInheritance
{
    /**
     * Save image Action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $model = $this->_initBanner();
        try {
            $bannerImages = $this->getRequest()->getParam(ConfigHelper::IMAGES_UPLOADED_ELEMENT_VAR_NAME);
            $this->imageUploader->uploadImages($bannerImages, $model);
            $this->cacheInvalidator->invalidate(Cache::TYPE_IDENTIFIER);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath('adminhtml/*/edit', ['id' => $model->getId()]);
        }

        return parent::execute();
    }

    /**
     * Load Banner from request
     *
     * @param string $idFieldName
     * @param string $storeFieldName
     * @return \Magento\Banner\Model\Banner $model
     */
    protected function _initBanner($idFieldName = 'banner_id', $storeFieldName = 'store_id')
    {
        $model = $this->_registry->registry('current_banner');
        if (!$model) {
            $model = parent::_initBanner($idFieldName, $storeFieldName);
        }
        return $model;
    }
}
