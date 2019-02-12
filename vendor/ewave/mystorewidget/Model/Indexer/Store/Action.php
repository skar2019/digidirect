<?php
namespace Ewave\MyStoreWidget\Model\Indexer\Store;

use Ewave\AbstractEntity\Api\Data\AbstractEntityInterface;
use Ewave\AbstractEntity\Model\ResourceModel\AbstractEntity\CollectionFactory;
use Ewave\MyStoreWidget\Model\ResourceModel\MyStoreIndex;
use Ewave\MyStoreWidget\Helper\Config as Helper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ObjectManager;

class Action
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var MyStoreIndex
     */
    protected $myStoreIndex;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Action constructor.
     * @param CollectionFactory $collectionFactory
     * @param MyStoreIndex $myStoreIndex
     * @param Helper $helper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        MyStoreIndex $myStoreIndex,
        Helper $helper,
        StoreManagerInterface $storeManager = null
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->myStoreIndex = $myStoreIndex;
        $this->helper = $helper;
        $this->storeManager = $storeManager ?: ObjectManager::getInstance()->get(StoreManagerInterface::class);
    }

    /**
     * @param array $ids
     * @return void
     */
    public function reindex(array $ids = [])
    {
        $attributes = $this->helper->getAttributes();
        $responseAttributes = $this->helper->getResponseAttributes();

        foreach ($this->storeManager->getStores(true) as $store) {
            $storeCollection = $this->collectionFactory->create();
            $storeCollection->setFlag('reindex', true);

            $storeCollection
                ->setStoreId($store->getId())
                ->addAttributeToSelect(AbstractEntityInterface::NAME, true)
                ->addAttributeToSelect($attributes, true)
                ->addAttributeToSelect($responseAttributes, true)
                ->addFieldToFilter(AbstractEntityInterface::STATUS, 1)
                ->addFieldToFilter(AbstractEntityInterface::ATTRIBUTE_SET_ID, ['in' => $this->helper->getEntities()]);

            $this->myStoreIndex->createTableFromSelect($storeCollection->getSelect(), $attributes, $store->getId());
        }
    }
}
