<?php
namespace Ewave\ProductAttachment\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Upload
 * @package Ewave\ProductAttachment\Controller\Adminhtml\Index
 */
class Upload extends Action
{
    /**
     * File Processor
     *
     * @var \Ewave\ProductAttachment\Model\FileProcessor
     */
    protected $fileProcessor;

    /**
     * Upload constructor.
     * @param Action\Context $context
     * @param \Ewave\ProductAttachment\Model\FileProcessor $fileProcessor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Ewave\ProductAttachment\Model\FileProcessor $fileProcessor
    ) {
        parent::__construct($context);
        $this->fileProcessor = $fileProcessor;
    }

    /**
     * Upload file controller action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $result = $this->fileProcessor->saveToTmp('file');
            $this->_getSession()->setProductAttachment($result);
            return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);

        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ewave_ProductAttachment::product_attachment_items_save');
    }
}
