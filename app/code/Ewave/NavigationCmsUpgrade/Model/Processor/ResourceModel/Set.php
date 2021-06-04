<?php
namespace Ewave\NavigationCMSUpgrade\Model\Processor\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class MenuItem
 *
 * @package Ewave\NavigationCMSUpgrade\Model\Processor\ResourceModel
 */
class Set extends AbstractDb
{
    /**
     * MenuItem constructor.
     *
     * @param Context $context
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ewave_navigation_menu_set', 'set_id');
    }
}
