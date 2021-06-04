<?php
namespace Ewave\NavigationCMSUpgrade\Model\Processor\Set\Column;

/**
 * Class Entity
 *
 * @package Ewave\NavigationCMSUpgrade\Model\Processor\Set
 */
class Entity extends AbstractFieldProcessor implements FieldProcessorInterface
{
    /**
     * @param string $code
     * @return null|string
     */
    public function getEntityIdByCode($code)
    {
        $connection = $this->menuSetResource->getConnection();
        $select = $connection->select()
            ->from('ewave_navigation_menu_set', ['set_id'])
            ->where($connection->quoteInto('set_code = ?', $code['set_code']));

        $result = $connection->fetchOne($select);
        return $result ?: null;
    }
}
