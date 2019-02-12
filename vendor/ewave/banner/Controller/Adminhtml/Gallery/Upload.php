<?php

namespace Ewave\Banner\Controller\Adminhtml\Gallery;

use Ewave\Banner\Acl\ConstantsAcl;
use Ewave\Banner\Component\Json;
use Magento\Backend\App\Action as BackendAction;
use Magento\Backend\App\Action\Context as BackendActionContext;
use Magento\Framework\Controller\Result\RawFactory as ResultRawfactory;
use Ewave\Banner\Model\Image\UploaderFactory as UploaderFactory;

class Upload extends BackendAction
{
    /**
     * Result raw object
     *
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var Json
     */
    protected $jsonHelper;

    /**
     * @var \Ewave\Banner\Model\Image\UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * Upload constructor.
     *
     * @param BackendActionContext $context
     * @param ResultRawfactory $resultRawFactory
     * @param Json $jsonHelper
     * @param UploaderFactory $uploaderFactory
     */
    public function __construct(
        BackendActionContext $context,
        ResultRawfactory $resultRawFactory,
        Json $jsonHelper,
        UploaderFactory $uploaderFactory
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->jsonHelper = $jsonHelper;
        $this->uploaderFactory = $uploaderFactory;
    }

    /**
     * Check ACL
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(ConstantsAcl::BANNER);
    }

    /**
     * Upload image
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        try {
            /**
             * @var $uploader \Ewave\Banner\Model\Image\Uploader
             */
            $uploader = $this->uploaderFactory->create();
            $result = $uploader->uploadImage();
            $result = $uploader->prepareResult($result);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        /** @var \Magento\Framework\Controller\Result\Raw $response */
        $response = $this->resultRawFactory->create();
        $response->setHeader('Content-type', 'text/plain');
        $response->setContents($this->jsonHelper->encode($result));
        return $response;
    }
}
