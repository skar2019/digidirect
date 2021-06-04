<?php

namespace Ewave\Banner\Inheritance\Magento\Banner\Controller\Adminhtml\Banner;

use Magento\Banner\Controller\Adminhtml\Banner\Save as MagentoSaveBannerAction;
use Magento\Backend\App\Action\Context as ActionContext;
use Magento\Framework\Registry;
use Magento\Banner\Model\Banner\Validator;
use Ewave\Banner\Model\Image\Uploader;
use Magento\Framework\App\Cache\TypeListInterface;
use Ewave\Banner\Model\Upload\ImageProcessor;

/**
 * EE/CE dependencies/inheritance control
 */
class Save extends MagentoSaveBannerAction
{
    /**
     * @var \Ewave\Banner\Model\Image\Uploader
     */
    protected $imageUploader;

    /**
     * @var TypeListInterface
     */
    protected $cacheInvalidator;

    /**
     * @var ImageProcessor
     */
    protected $imageProcessor;

    /**
     * Save constructor.
     *
     * @param ActionContext $context
     * @param Registry $registry
     * @param Validator $bannerValidator
     * @param Uploader $bannerHelperUpload
     * @param TypeListInterface $typeList
     * @param ImageProcessor $imageProcessor
     */
    public function __construct(
        ActionContext $context,
        Registry $registry,
        Validator $bannerValidator,
        Uploader $bannerHelperUpload,
        TypeListInterface $typeList,
        ImageProcessor $imageProcessor
    ) {
        $this->cacheInvalidator = $typeList;
        $this->imageUploader = $bannerHelperUpload;
        $this->imageProcessor = $imageProcessor;
        parent::__construct($context, $registry, $bannerValidator);
    }
}
