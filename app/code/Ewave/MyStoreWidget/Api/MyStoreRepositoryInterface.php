<?php
namespace Ewave\MyStoreWidget\Api;

use Ewave\AbstractEntity\Api\Data\AbstractEntityInterface;
use Ewave\MyStoreWidget\Api\Data\MyStoreInterface;
use Ewave\AbstractEntity\Model\ResourceModel\AbstractEntity\Collection as AbstractEntityCollection;
use Magento\Customer\Model\Customer;
use Magento\Framework\DataObject;

/**
 * Interface MyStoreRepositoryInterface
 * @package Ewave\MyStoreWidget\Api
 */
interface MyStoreRepositoryInterface
{
    /**
     * @param MyStoreInterface $data
     * @return MyStoreInterface
     */
    public function save(MyStoreInterface $data);

    /**
     * @param int $customerId
     * @param string $type
     * @return MyStoreInterface
     */
    public function getByCustomerId($customerId, $type = MyStoreInterface::DEFAULT_TYPE);

    /**
     * @param int $customerId
     * @return bool
     */
    public function deleteByCustomerId($customerId);

    /**
     * @return AbstractEntityCollection
     */
    public function getStoresCollection();

    /**
     * @param DataObject $address
     * @return bool|AbstractEntityInterface
     */
    public function getStoreByAddress(DataObject $address);

    /**
     * @param Customer $customer
     * @param DataObject $address
     * @return bool
     */
    public function setMyStoreByShippingAddress(Customer $customer, DataObject $address = null);
}
