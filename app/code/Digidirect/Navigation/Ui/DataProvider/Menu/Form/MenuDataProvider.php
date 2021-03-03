<?php

namespace Digidirect\Navigation\Ui\DataProvider\Menu\Form;

use Digidirect\Navigation\Model\ResourceModel\Menu\Grid\CollectionFactory as MenuCollectionFactory;
use Digidirect\Navigation\Model\Menu as Menu;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class MenuDataProvider extends AbstractDataProvider
{
    /**
     * @var PoolInterface
     */
    protected $pool;

    /**
     * MenuDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param MenuCollectionFactory $menuCollectionFactory
     * @param PoolInterface $pool
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        MenuCollectionFactory $menuCollectionFactory,
        PoolInterface $pool,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $menuCollectionFactory->create();
        $this->pool = $pool;
    }

    /**
     * Used to be able modify data
     *
     * @return array
     */
    public function getData()
    {
        $items = $this->collection;

        /**
         * @var Menu $menuItem
         */
        foreach ($items as $menuItem) {
            $result = $menuItem->getData();
            $this->data[$menuItem->getId()] = $result;
        }

        /** @var ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $this->data = $modifier->modifyData($this->data ?? []);
        }

        return $this->data;
    }

    /**
     * Modify Meta
     *
     * @return array
     */
    public function getMeta()
    {
        $metaOriginal = parent::getMeta();
        /** @var ModifierInterface; $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($metaOriginal);
            $metaOriginal = array_replace_recursive($metaOriginal, $meta);
        }
        return $metaOriginal;
    }
}
