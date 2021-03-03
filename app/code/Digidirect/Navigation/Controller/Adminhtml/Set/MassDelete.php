<?php

namespace Digidirect\Navigation\Controller\Adminhtml\Set;

use Digidirect\Navigation\Api\SetRepositoryInterface;
use Digidirect\Navigation\Controller\Adminhtml\MassDeleteAbstract;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Digidirect\Navigation\Model\ResourceModel\Set\CollectionFactory;

/**
 * Class MassDelete
 * @package Digidirect\Navigation\Controller\Adminhtml\Set
 */
class MassDelete extends MassDeleteAbstract
{
    const ADMIN_RESOURCE = 'Digidirect_Navigation::navigation_menu_sets_delete';

    /**
     * MassDelete constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param SetRepositoryInterface $repository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        SetRepositoryInterface $repository,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context, $filter, $repository);
        $this->collectionFactory = $collectionFactory;
    }
}
