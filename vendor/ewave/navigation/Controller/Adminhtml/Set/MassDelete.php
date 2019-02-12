<?php

namespace Ewave\Navigation\Controller\Adminhtml\Set;

use Ewave\Navigation\Api\SetRepositoryInterface;
use Ewave\Navigation\Controller\Adminhtml\MassDeleteAbstract;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Ewave\Navigation\Model\ResourceModel\Set\CollectionFactory;

/**
 * Class MassDelete
 * @package Ewave\Navigation\Controller\Adminhtml\Set
 */
class MassDelete extends MassDeleteAbstract
{
    const ADMIN_RESOURCE = 'Ewave_Navigation::navigation_menu_sets_delete';

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
