<?php
namespace Ewave\AI\Model\Lib\Entity\Import\CustomerComposite;

use Ewave\AI\Model\Lib\Entity\Import\ImportInterface;

/**
 * Interface CustomerInterface
 */
interface CustomerCompositeInterface extends ImportInterface
{
    /**
     * Delete single customer
     *
     * @param string $customerEmail
     * @param string $websiteCode
     * @return mixed
     */
    public function deleteCustomer($customerEmail, $websiteCode);

    /**
     * Delete several customers
     *
     *
     *
     * @param array $customers
     * @return mixed
     */
    public function deleteBunch(array $customers);
}
