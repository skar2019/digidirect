<?php

namespace Ewave\Banner\Controller\Adminhtml\Banner;

use Magento\Framework\Controller\ResultFactory;

class Upload extends \Magento\Backend\App\Action
{
    /**
     * @var \Ewave\Banner\Model\Upload\ImageProcessor
     */
    protected $imageProcessor;

    /**
     * AbstractUpload constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Ewave\Banner\Model\Upload\ImageProcessor $imageProcessor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Ewave\Banner\Model\Upload\ImageProcessor $imageProcessor
    ) {
        parent::__construct($context);
        $this->imageProcessor = $imageProcessor;
    }


    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        try {
            $files = $this->getRequest()->getFiles();
            $result = $this->imageProcessor->saveFileToTmpDir($files);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
