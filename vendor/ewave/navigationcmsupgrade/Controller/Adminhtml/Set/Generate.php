<?php
namespace Ewave\NavigationCMSUpgrade\Controller\Adminhtml\Set;

use Magento\Backend\App\Action\Context;
use Ewave\NavigationCMSUpgrade\Model\Entity\Set;
use Magento\Ui\Component\MassAction\Filter;
use Ewave\Navigation\Model\ResourceModel\Set\Grid\CollectionFactory;

/**
 * Class Generate
 *
 * @package Ewave\NavigationCMSUpgrade\Controller\Adminhtml\Set
 */
class Generate extends \Ewave\CmsUpgrade\Controller\Adminhtml\Generate
{
    /**
     * @var Set
     */
    protected $set;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Generate constructor.
     *
     * @param Context $context
     * @param Set $set
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Set $set,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->set = $set;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /**
         * @var $menuCollection \Ewave\Navigation\Model\ResourceModel\Menu\Collection
         */
        $menuCollection = $this->set->getCollection();
        $collection = $this->filter->getCollection($menuCollection);
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$collection->getSize()) {
            $this->messageManager->addWarningMessage(__('Please select items to generate'));
            return $resultRedirect->setRefererOrBaseUrl();
        }

        $this->set->setParams($collection);
        try {
            $result = $this->set->generate();
            $this->setGenerateResult($result);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        return $resultRedirect->setRefererOrBaseUrl();
    }
}
