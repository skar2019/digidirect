<?php

namespace Ewave\Banner\Preference\Magento\Banner\Controller\Adminhtml\Banner;

use Ewave\Banner\Helper\Image\Config as ConfigHelper;
use Magento\Banner\Model\Banner\Validator as BannerValidator;
use Magento\Banner\Model\Banner as BannerModel;
use Magento\Banner\Controller\Adminhtml\Banner\Save as BannerSaveAction;

class Save extends BannerSaveAction
{
    /**
     * @var \Ewave\Banner\Model\Image\Uploader
     */
    protected $imageUploader;

    /**
     * Save constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param BannerValidator $bannerValidator
     * @param \Ewave\Banner\Model\Image\Uploader $bannerHelperUpload
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        BannerValidator $bannerValidator,
        \Ewave\Banner\Model\Image\Uploader $bannerHelperUpload
    ) {
        $this->imageUploader = $bannerHelperUpload;
        parent::__construct($context, $registry, $bannerValidator);
    }

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
     * @return BannerModel $model
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
