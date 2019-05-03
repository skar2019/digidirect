<?php

namespace Ewave\Blog\Controller\Archive;

use Ewave\Blog\Block\Archive;
use Ewave\Blog\Helper\Data;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Registry;
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
     * @var Registry
     */
    protected $registry;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Data $dataHelper
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Data $dataHelper,
        Registry $registry
    ) {
        $this->dataHelper = $dataHelper;
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     * @throws NotFoundException
     */
    public function execute()
    {
        $date = $this->getRequest()->getParam('date');
        $date = explode('-', $date);
        $date[2] = '01';
        $time = strtotime(implode('-', $date));

        if (!$time || count($date) != 3) {
            throw new NotFoundException(__('Page not found.'));
        }

        $this->registry->register(Archive::CURRENT_YEAR, (int)$date[0]);
        $this->registry->register(Archive::CURRENT_MONTH, (int)$date[1]);

        $page = $this->resultPageFactory->create();
        $page->getConfig()->getTitle()->set(__('Monthly Archives: ' . date('F', $time) . ' ' . date('Y', $time)));
        $breadcrumbShow = $this->dataHelper->getGeneralSettingsConfig('breadcrumb');
        if ($breadcrumbShow) {
            $this->addBreadcrumbs($page);
        }
        return $page;
    }

    /**
     * @param \Magento\Framework\View\Result\Page $page
     */
    private function addBreadcrumbs(\Magento\Framework\View\Result\Page $page)
    {
        $breadcrumbs = $page->getLayout()->getBlock('breadcrumbs');
        if (!$breadcrumbs) {
            return;
        }
        $breadcrumbs->addCrumb(
            'home',
            [
                'label' => __('Home'),
                'title' => __('Home'),
                'link' => $this->_url->getUrl(''),
            ]
        );
        $breadcrumbs->addCrumb(
            'ewave_blog',
            [
                'label' => __('Latest Blog Posts'),
                'title' => __('Latest Blog Posts'),
            ]
        );
    }
}
