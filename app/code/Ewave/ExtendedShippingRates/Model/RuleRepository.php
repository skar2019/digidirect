<?php
namespace Ewave\ExtendedShippingRates\Model;

use Ewave\ExtendedShippingRates\Api\RuleRepositoryInterface;
use Ewave\ExtendedShippingRates\Model\ResourceModel\Rule as RuleResource;
use Ewave\ExtendedShippingRates\Model\RuleFactory;
use Magento\Framework\Model\AbstractModel;

class RuleRepository extends AbstractRepository implements RuleRepositoryInterface
{
    /**
     * RuleRepository constructor.
     * @param RuleResource $resource
     * @param RuleFactory $factory
     */
    public function __construct(
        RuleResource $resource,
        RuleFactory $factory
    ) {
        parent::__construct($resource, $factory);
    }

    /**
     * Save data
     *
     * @param AbstractModel $model
     * @return AbstractModel
     * @throws CouldNotSaveException
     */
    public function save(AbstractModel $model)
    {
        /**
         * Prepare store Ids if applicable and if they were set as string in comma separated format.
         * Backwards compatibility.
         */
        if ($model->hasStoreIds()) {
            $storeIds = $model->getStoreIds();
            if (!empty($storeIds)) {
                $model->setStoreIds($storeIds);
            }
        }
        return parent::save($model);
    }
}
