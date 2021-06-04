<?php
namespace Ewave\CmsUpgrade\Controller\Adminhtml\Block;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory;
use Ewave\CmsUpgrade\Model\GeneratorInterface;

/**
 * Class Generate
 * @package Ewave\CmsUpgrade\Controller\Adminhtml\Block
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
     * @var Block
     */
    protected $block;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param GeneratorInterface $block
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        GeneratorInterface $block
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->block = $block;
        parent::__construct($context);
    }

    /**
     * @return $this
     */
    public function execute()
    {
        /** @var \Magento\Cms\Model\ResourceModel\AbstractCollection $collection */
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        $this->block->setCollection($collection);
        try {
            $result = $this->block->generate();
            $this->setGenerateResult($result);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setRefererOrBaseUrl();
    }
}
