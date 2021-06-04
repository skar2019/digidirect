<?php

namespace Ewave\ProductCalculator\Controller\Adminhtml;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class AbstractMassAction
 * @package Ewave\ProductCalculator\Controller\Adminhtml
 */
abstract class AbstractMassAction extends \Magento\Backend\App\Action
{
    /**
     * @var string
     */
    protected $redirectUrl = '*/*/index';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollectionFactory
     */
    protected $collectionFactory;

    /**
     * MassStatus constructor.
     * @param Context $context
     * @param Filter $filter
     */
    public function __construct(
        Context $context,
        Filter $filter
    ) {
        parent::__construct($context);
        $this->filter = $filter;
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            return $this->massAction($collection);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath($this->redirectUrl);
        }
    }

    /**
     * Process mass status change
     *
     * @param AbstractCollection|\Magento\Framework\Data\Collection\AbstractDb $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
    {
        $params = $this->getRequest()->getParams();
        $recordsAffected = $this->doAction($collection->getAllIds(), $params);
        if ($recordsAffected) {
            $this->addSuccessMessage($recordsAffected);
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($this->getComponentRefererUrl());

        return $resultRedirect;
    }

    /**
     * Do the main action
     *
     * @param int[] $entityIds
     * @param array $params
     * @return mixed
     */
    abstract protected function doAction($entityIds, $params = null);

    /**
     * Add success message
     *
     * @param int $recordsAffected
     * @return void
     */
    protected function addSuccessMessage($recordsAffected)
    {
        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were updated.', $recordsAffected));
    }

    /**
     * Return component referer url
     *
     * @return null|string
     */
    protected function getComponentRefererUrl()
    {
        return $this->filter->getComponentRefererUrl() ?: $this->redirectUrl;
    }
}
