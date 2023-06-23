<?php
/**
 * Class Abandoned
 *
 * PHP version 7
 *
 * @category Sparsh
 * @package  Sparsh_AbandonedCart
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
namespace Sparsh\AbandonedCart\Model;

/**
 * Class Abandoned
 *
 * @category Sparsh
 * @package  Sparsh_AbandonedCart
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class Abandoned extends \Magento\Framework\Model\AbstractModel
{
    /**
     * AbandonedCollection
     *
     * @var ResourceModel\Abandoned\Collection
     */
    public $abandonedCollectionFactory;

    /**
     * DateTime
     *
     * @var \Magento\Framework\Stdlib\DateTime
     */
    public $dateTime;

    /**
     * Abandoned constructor.
     *
     * @param \Magento\Framework\Model\Context                             $context            context
     * @param \Magento\Framework\Registry                                  $registry           registry
     * @param ResourceModel\Abandoned\CollectionFactory                    $abandoned          abandoned
     * @param \Magento\Framework\Stdlib\DateTime                           $dateTime           dateTime
     * @param array                                                        $data               data
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource           resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection resourceCollection
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Sparsh\AbandonedCart\Model\ResourceModel\Abandoned\CollectionFactory $abandoned,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        array $data = [],
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null
    ) {
        $this->abandonedCollectionFactory = $abandoned;
        $this->dateTime     = $dateTime;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Constructor.
     *
     * @return null
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init(\Sparsh\AbandonedCart\Model\ResourceModel\Abandoned::class);
    }

    /**
     * Prepare data to be saved to database.
     *
     * @return $this
     */
    public function beforeSave()
    {
        parent::beforeSave();
        if ($this->isObjectNew()) {
            $this->setCreatedAt($this->dateTime->formatDate(true));
        }
        $this->setUpdatedAt($this->dateTime->formatDate(true));

        return $this;
    }

    /**
     * LoadByQuoteId
     *
     * @param int $quoteId quoteId
     *
     * @return mixed
     */
    public function loadByQuoteId($quoteId, $storeId)
    {
        $collection = $this->abandonedCollectionFactory->create()
            ->addFieldToFilter('quote_id', $quoteId)
            ->addFieldToFilter('store_id', $storeId)
            ->setPageSize(1);

        return $collection->getFirstItem();
    }
}
