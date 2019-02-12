<?php
namespace Ewave\CmsUpgrade\Controller\Adminhtml\Banner;

use Magento\Backend\App\Action\Context;
use Ewave\CmsUpgrade\Model\Entity\Banner;

/**
 * Class Generate
 * @package Ewave\CmsUpgrade\Controller\Adminhtml\Banner
 */
class Generate extends \Ewave\CmsUpgrade\Controller\Adminhtml\Generate
{
    /**
     * @var Banner
     */
    protected $banner;

    /**
     * @param Context $context
     * @param Banner $banner
     */
    public function __construct(
        Context $context,
        Banner $banner
    ) {
        $this->banner = $banner;
        parent::__construct($context);
    }

    /**
     * @return $this
     */
    public function execute()
    {
        $ids = $this->getRequest()->getParam('banner');
        if (!is_array($ids)) {
            $this->messageManager->addErrorMessage(__('Please select a banner(s).'));
        } else {
            $this->banner->setBannerIds($ids);
            try {
                $result = $this->banner->generate();
                $this->setGenerateResult($result);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setRefererOrBaseUrl();
    }
}
