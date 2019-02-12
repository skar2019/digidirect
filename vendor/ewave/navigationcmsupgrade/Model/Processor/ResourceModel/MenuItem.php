<?php
namespace Ewave\NavigationCMSUpgrade\Model\Processor\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class MenuItem
 *
 * @package Ewave\NavigationCMSUpgrade\Model\Processor\ResourceModel
 */
class MenuItem extends AbstractDb
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
        $this->_init('ewave_navigation_menu_entity', 'entity_id');
    }

    /**
     * @param string $code
     * @return string
     */
    public function getIdByCode($code)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), ['entity_id'])
            ->where($connection->quoteInto('menu_item_code = ?', $code));

        return $connection->fetchOne($select);
    }

    /**
     * @param string $code
     * @return string
     */
    public function getParentIdByParentCode($code)
    {
        return $this->getIdByCode($code);
    }
}
