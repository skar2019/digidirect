<?php
namespace Digidirect\Faq\Controller\Adminhtml\Faq;

use Digidirect\Faq\Api\AbstractFaqInterface;
use Digidirect\Faq\Controller\Adminhtml\MassDeleteAbstract;
use Digidirect\Faq\Api\FaqRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Digidirect\Faq\Model\ResourceModel\Faq\CollectionFactory;

/**
 * Class MassDelete
 * @package Digidirect\Faq\Controller\Adminhtml\Faq
 */
class MassDelete extends MassDeleteAbstract
{
    const ADMIN_RESOURCE = 'Digidirect_Faq::faq_items_delete';

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
