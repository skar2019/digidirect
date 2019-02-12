<?php
namespace Ewave\NavigationCMSUpgrade\Model\Processor\MenuItem\Column;

/**
 * Class Info
 *
 * @package Ewave\NavigationCMSUpgrade\Model\Processor
 */
class Set extends AbstractProcessor implements FieldProcessorInterface
{
    /**
     * @param array $data
     * @param string $key
     * @return mixed|null
     */
    public function getData(array $data, $key)
    {
        return $data[$key] ?? null;
    }

    /**
     * @param array $itemData
     * @return array
     */
    public function getSets($itemData)
    {
        $setCodes = explode(',', $itemData['set_code']);

        $connection = $this->menuItemResource->getConnection();
        $select = $connection->select()
            ->from('ewave_navigation_menu_set', ['set_id'])
            ->where($connection->quoteInto('set_code IN (?)', $setCodes));
        $sets = $connection->fetchCol($select);
        if (empty($sets)) {
            foreach ($setCodes as $setCode) {
                $sets[] = $connection->insert(
                    'ewave_navigation_menu_set',
                    ['set_code' => $setCode, 'status' => 1, 'name' => $setCode]
                );
            }
        }

        return $sets;
    }
}
