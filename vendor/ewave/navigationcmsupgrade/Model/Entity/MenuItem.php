<?php
namespace Ewave\NavigationCMSUpgrade\Model\Entity;

use Ewave\NavigationCMSUpgrade\Model\Menu\Generator as MenuGenerator;
use Ewave\Navigation\Model\ResourceModel\Menu\CollectionFactory;
use Ewave\Navigation\Model\ResourceModel\Menu\Collection;

/**
 * Class MenuItem
 *
 * @package Ewave\NavigationCMSUpgrade\Model\Entity
 */
class MenuItem extends AbstractEntity
{
    const ENTITY_TYPE = 'menu_item';

    /**
     * @var []
     */
    protected $params;

    /**
     * @var MenuGenerator
     */
    protected $generator;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * MenuItem constructor.
     *
     * @param MenuGenerator $generator
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(MenuGenerator $generator, CollectionFactory $collectionFactory)
    {
        $this->generator = $generator;
        $this->generator->setGenerateEntity($this);
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return string
     */
    public function getEntityType()
    {
        return static::ENTITY_TYPE;
    }

    /**
     * @return Collection
     */
    public function getCollection()
    {
        /**
         * @var $collection Collection
         */
        $collection = $this->collectionFactory->create();
        $collection->joinTypeInfo();

        $collection->getSelect()->joinLeft(
            ['menu_item_set_link' => $collection->getTable('ewave_navigation_menu_set_link')],
            'entity_id = menu_item_set_link.menu_entity_id',
            ['menu_item_set_link' => '*']
        );

        $collection->getSelect()->joinLeft(
            ['menu_set' => $collection->getTable('ewave_navigation_menu_set')],
            'menu_item_set_link.menu_set_id = menu_set.set_id',
            [
                'set_id' => new \Zend_Db_Expr("GROUP_CONCAT(menu_set.set_id SEPARATOR ',')"),
                'set_code' => new \Zend_Db_Expr("GROUP_CONCAT(menu_set.set_code SEPARATOR ',')")
            ]
        );

        return $collection;
    }
}
