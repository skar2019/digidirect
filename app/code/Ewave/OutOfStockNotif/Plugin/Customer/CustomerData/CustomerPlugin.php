<?php

namespace Ewave\OutOfStockNotif\Plugin\Customer\CustomerData;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\CustomerData\Customer as Subject;

/**
 * Class CustomerPlugin
 * @package Ewave\OutOfStockNotif\Plugin\Customer\CustomerData
 */
class CustomerPlugin
{
    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @param CurrentCustomer $currentCustomer
     */
    public function __construct(
        CurrentCustomer $currentCustomer
    ) {
        $this->currentCustomer = $currentCustomer;
    }
    
    /**
     * @param Subject $subject
     * @param array $result
     * @return array
     */
    public function afterGetSectionData(Subject $subject, $result)
    {
        if (!$this->currentCustomer->getCustomerId()) {
            return [];
        }
        $customer = $this->currentCustomer->getCustomer();
        $result['email'] = $customer->getEmail();
        return $result;
    }
}

