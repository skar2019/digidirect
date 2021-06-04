<?php
namespace Ewave\NavigationCMSUpgrade\Model\Entity;

use Ewave\NavigationCMSUpgrade\Model\Set\Generator as MenuGenerator;
use Ewave\Navigation\Model\ResourceModel\Set\CollectionFactory;
use Ewave\Navigation\Model\ResourceModel\Set\Collection;

/**
 * Class MenuItem
 *
 * @package Ewave\NavigationCMSUpgrade\Model\Entity
 */
class Set extends AbstractEntity
{
    const ENTITY_TYPE = 'set';

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
     * Set constructor.
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
     * @return Collection
     */
    public function getCollection()
    {
        /**
         * @var $collection Collection
         */
        $collection = $this->collectionFactory->create();
        return $collection;
    }

    /**
     * @return string
     */
    public function getEntityType()
    {
        return static::ENTITY_TYPE;
    }
}
