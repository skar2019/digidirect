<?php
namespace Ewave\Faq\Controller\Adminhtml\Faq;

use Ewave\Faq\Api\AbstractFaqInterface;
use Ewave\Faq\Controller\Adminhtml\MassDeleteAbstract;
use Ewave\Faq\Api\FaqRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Ewave\Faq\Model\ResourceModel\Faq\CollectionFactory;

/**
 * Class MassDelete
 * @package Ewave\Faq\Controller\Adminhtml\Faq
 */
class MassDelete extends MassDeleteAbstract
{
    const ADMIN_RESOURCE = 'Ewave_Faq::faq_items_delete';

    /**
     * @var AbstractFaqInterface
     */
    protected $repository;
    
    /**
     * MassDelete constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param FaqRepositoryInterface $faqRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        FaqRepositoryInterface $faqRepository,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context, $filter, $faqRepository);
        $this->collectionFactory = $collectionFactory;
    }
}
