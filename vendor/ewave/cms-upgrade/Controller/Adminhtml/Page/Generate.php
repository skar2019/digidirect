<?php
namespace Ewave\CmsUpgrade\Controller\Adminhtml\Page;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory;
use Ewave\CmsUpgrade\Model\Entity\AbstractEntity;

/**
 * Class Generate
 * @package Ewave\CmsUpgrade\Controller\Adminhtml\Page
 */
class Generate extends \Ewave\CmsUpgrade\Controller\Adminhtml\Generate
{

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var string
     */
    protected $entityLabel = '';

    /**
     * @var AbstractEntity
     */
    protected $page;

    /**
     * Generate constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param AbstractEntity $page
     * @param $entityLabel
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        AbstractEntity $page,
        $entityLabel
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->page = $page;
        $this->entityLabel = $entityLabel;
        parent::__construct($context);
    }

    /**
     * @return $this|bool
     */
    public function execute()
    {
        $ids = $this->getPageIds();

        if (!is_array($ids)) {
            $this->messageManager->addErrorMessage(__('Please select a ' . $this->entityLabel . '(s).'));
        } else {
            $this->page->setItemsIds($ids);
            try {
                $result = $this->page->generate();
                $this->setGenerateResult($result);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setRefererOrBaseUrl();
    }

    /**
     * @return array
     */
    protected function getPageIds()
    {
        $ids = $this->getRequest()->getParam(Filter::SELECTED_PARAM) ?? null;
        if (!$ids && 'false' === $this->getRequest()->getParam(Filter::EXCLUDED_PARAM)) {

            /** @var \Magento\Cms\Model\ResourceModel\AbstractCollection $collection */
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $ids = $collection->getAllIds();
        }
        return $ids;
    }
}