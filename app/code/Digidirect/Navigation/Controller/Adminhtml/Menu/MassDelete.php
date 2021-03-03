<?php

namespace Digidirect\Navigation\Controller\Adminhtml\Menu;

use Digidirect\Navigation\Api\MenuRepositoryInterface;
use Digidirect\Navigation\Controller\Adminhtml\MassDeleteAbstract;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Digidirect\Navigation\Model\ResourceModel\Menu\CollectionFactory;

/**
 * Class MassDelete
 * @package Digidirect\Navigation\Controller\Adminhtml\Menu
 */
class MassDelete extends MassDeleteAbstract
{
    const ADMIN_RESOURCE = 'Digidirect_Navigation::navigation_menu_items_delete';

    /**
     * MassDelete constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param MenuRepositoryInterface $menuRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        MenuRepositoryInterface $menuRepository,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context, $filter, $menuRepository);
        $this->collectionFactory = $collectionFactory;
    }
}
