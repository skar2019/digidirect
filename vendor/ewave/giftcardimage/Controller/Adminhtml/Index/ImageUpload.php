<?php
namespace Ewave\GiftCardImage\Controller\Adminhtml\Index;

use Ewave\GiftCardImage\Model\Media\ImageProcessor;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class ImageUpload
 * @package Ewave\GiftCardImage\Controller\Adminhtml\Index
 */
class ImageUpload extends Action
{
    /**
     * @var ImageProcessor
     */
    protected $imageProcessor;

    /**
     * ImageUpload constructor.
     * @param Context $context
     * @param ImageProcessor $imageProcessor
     */
    public function __construct(
        Context $context,
        ImageProcessor $imageProcessor
    ) {
        parent::__construct($context);
        $this->imageProcessor = $imageProcessor;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $files = $this->getRequest()->getFiles();
        $result = $this->imageProcessor->saveToTmp(key($files));
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
