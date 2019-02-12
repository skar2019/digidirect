<?php

namespace Ewave\CmsUpgrade\Controller\Adminhtml\Config;

use Magento\Backend\App\Action\Context;
use Ewave\CmsUpgrade\Model\Entity\Config;

/**
 * Class Generate
 */
class Generate extends \Ewave\CmsUpgrade\Controller\Adminhtml\Generate
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Context $context
     * @param Config $config
     */
    public function __construct(
        Context $context,
        Config $config
    ) {
        $this->config = $config;
        parent::__construct($context);
    }

    /**
     * @return $this
     */
    public function execute()
    {
        /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $checked = $this->getIsChecked();
        if (empty($checked)) {
            $this->messageManager->addWarningMessage(__('Please choose section to generate'));
            return $resultRedirect->setRefererOrBaseUrl();
        }
        $params = $this->prepareParams();
        $this->config->setParams($params);
        try {
            $result = $this->config->generate();
            $this->setGenerateResult($result);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setRefererOrBaseUrl();
    }

    /**
     * @return array
     */
    protected function prepareParams()
    {
        return [
            'groups' => $this->getRequest()->getPost('groups'),
            'files' => $this->getRequest()->getFiles('groups'),
            'section' => $this->getRequest()->getParam('section'),
            'website' => $this->getRequest()->getParam('website'),
            'store' => $this->getStore(),
            'checked' => $this->getIsChecked(),
        ];
    }

    /**
     * @return mixed|null|string
     */
    protected function getStore()
    {
        $store = null;
        if (!$this->isWebsiteLevel()) {
            $store = $this->getRequest()->getParam('store') != 'default'
                ? $this->getRequest()->getParam('store')
                : '0';
        }
        return $store;
    }

    /**
     * @return bool
     */
    protected function isWebsiteLevel()
    {
        return $this->getRequest()->getParam('website') != null;
    }

    /**
     * @return mixed
     */
    protected function getIsChecked()
    {
        return $this->getRequest()->getPost('generate');
    }
}
