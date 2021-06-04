<?php

namespace Ewave\Feed\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Ewave\Feed\Model\FeedFactory;
use Ewave\Feed\Model\FeedRepository;

abstract class Feed extends Action
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var FeedFactory
     */
    protected $feedFactory;

    /**
     * @var feedRepository
     */
    protected $feedRepository;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * Feed constructor.
     * @param Context $context
     * @param Registry $registry
     * @param FeedFactory $feedFactory
     * @param FeedRepository $feedRepository
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FeedFactory $feedFactory,
        FeedRepository $feedRepository
    ) {
        $this->context = $context;
        $this->registry = $registry;
        $this->feedFactory = $feedFactory;
        $this->feedRepository = $feedRepository;
        $this->backendSession = $context->getSession();

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     * @param \Magento\Backend\Model\View\Result\Page\Interceptor $resultPage
     * @return \Magento\Backend\Model\View\Result\Page\Interceptor
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Ewave_Utilities::ewave');
        $resultPage->getConfig()->getTitle()->prepend(__('Shopping Feeds'));
        $resultPage->getConfig()->getTitle()->prepend(__('Feeds'));

        return $resultPage;
    }

    /**
     * Current feed model
     * @return \Ewave\Feed\Model\Feed
     */
    protected function initModel()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $model = $this->feedRepository->getById($id);
        } else {
            $model = $this->feedFactory->create();
        }

        $this->registry->register('current_model', $model);

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Ewave_Feed::feed_feed');
    }
}
