<?php
namespace Ewave\Blog\Controller\Index;

use Ewave\Blog\Helper\Data;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 */
class Index extends Action
{
    /**
     * @var Data
     */
    protected $dataHelper;
    
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Data $dataHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Data $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     * @throws NotFoundException
     */
    public function execute()
    {
        if (!$this->dataHelper->isModuleEnabled()) {
            throw new NotFoundException(__('Page not found.'));
        }
        $page = $this->resultPageFactory->create();
        $searchQuery = $this->getRequest()->getParam('s');
        $title = !empty($searchQuery) ? __("Search results for: '%1'", $searchQuery) : __('Latest Blog Posts');
        $page->getConfig()->getTitle()->set($title);
        $breadcrumbShow = $this->dataHelper->getGeneralSettingsConfig('breadcrumb');
        if ($breadcrumbShow) {
            $breadcrumbs = $page->getLayout()
                ->getBlock('breadcrumbs');
            $breadcrumbs->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Home'),
                    'link' => $this->_url->getUrl('')
                ]
            );
            $breadcrumbs->addCrumb(
                'ewave_blog',
                [
                    'label' => __($title),
                    'title' => __($title)
                ]
            );
        }
        $pageLayout = $this->dataHelper->getGeneralSettingsConfig('post_list_layout');
        $pageConfig = $page->getConfig();
        $pageConfig->setPageLayout($pageLayout);
        $page->getLayout()->getUpdate();
        return $page;
    }
}
