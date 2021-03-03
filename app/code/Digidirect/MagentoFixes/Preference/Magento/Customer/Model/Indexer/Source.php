<?php

namespace Digidirect\MagentoFixes\Preference\Magento\Customer\Model\Indexer;

use Magento\Customer\Model\Indexer\Source as IndexerSource;
use Magento\Customer\Model\ResourceModel\Customer\Indexer\Collection;
use Magento\Customer\Model\ResourceModel\Customer\Indexer\CollectionFactory;
use Magento\Framework\App\ResourceConnection\SourceProviderInterface;
use Traversable;

/**
 * Customers data batch generator for customer_grid indexer
 */
class Source extends IndexerSource implements \IteratorAggregate, \Countable, SourceProviderInterface
{
    /**
     * @var Collection
     */
    protected $customerCollection;

    /**
     * @var int
     */
    protected $batchSize;

    /**
     * Source constructor.
     * @param CollectionFactory $collectionFactory
     * @param int               $batchSize
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        $batchSize = 10000
    ) {
        $this->customerCollection = $collectionFactory->create();
        $this->batchSize = $batchSize;
    }

    /**
     * {@inheritdoc}
     */
    public function getMainTable()
    {
        return $this->customerCollection->getMainTable();
    }

    /**
     * {@inheritdoc}
     */
    public function getIdFieldName()
    {
        return $this->customerCollection->getIdFieldName();
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToSelect($fieldName, $alias = null)
    {
        $this->customerCollection->addFieldToSelect($fieldName, $alias);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSelect()
    {
        return $this->customerCollection->getSelect();
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($attribute, $condition = null)
    {
        $this->customerCollection->addFieldToFilter($attribute, $condition);

        return $this;
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->customerCollection->getSize();
    }

    /**
     * Retrieve an iterator
     *
     * @return Traversable
     */
    public function getIterator()
    {
        $this->customerCollection->setPageSize($this->batchSize);
        $lastPage = $this->customerCollection->getLastPageNumber();
        $pageNumber = 0;
        do {
            $this->customerCollection->clear();
            $this->customerCollection->setCurPage($pageNumber);
            foreach ($this->customerCollection->getItems() as $key => $value) {
                yield $key => $value;
            }
            $pageNumber++;
        } while ($pageNumber <= $lastPage);
    }

    // REF-PATCH START added missing function addAttributeToSelect
    /**
     * {@inheritdoc}
     */
    public function addAttributeToSelect($fieldName, $alias = null)
    {
        $this->customerCollection->addAttributeToSelect($fieldName, $alias);

        return $this;
    }
    //REF-PATCH END
}
