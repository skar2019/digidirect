<?php
/**
 * Customer Private Data Section Provider
 *
 * File Location: app/code/Digidirect/Customer/CustomerData/CustomerPrivateData.php
 */
namespace Digidirect\Customer\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\UrlInterface;
use Digidirect\Customer\ViewModel\Customer as CustomerViewModel;

class CustomerPrivateData implements SectionSourceInterface
{
    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var CustomerViewModel
     */
    protected $customerViewModel;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param CurrentCustomer $currentCustomer
     * @param CustomerViewModel $customerViewModel
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        CurrentCustomer $currentCustomer,
        CustomerViewModel $customerViewModel,
        UrlInterface $urlBuilder
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->customerViewModel = $customerViewModel;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get section data
     *
     * @return array
     */
    public function getSectionData()
    {
        $isLoggedIn = $this->customerViewModel->isLoggedIn();
        $isMobile = $this->customerViewModel->isMobile();

        $data = [
            'is_logged_in' => $isLoggedIn,
            'is_mobile' => $isMobile,
            'wishlist_url' => $this->urlBuilder->getUrl('wishlist'),
            'login_url' => $this->urlBuilder->getUrl('customer/account/login'),
            'account_url' => $this->urlBuilder->getUrl('customer/account'),
            'logout_url' => $this->urlBuilder->getUrl('customer/account/logout'),
        ];

        if ($isLoggedIn) {
            $data['customer_firstname'] = $this->customerViewModel->getFirstName();
            $data['wishlist_count'] = $this->customerViewModel->getWishlistItemsCount();
        } else {
            $data['customer_firstname'] = '';
            $data['wishlist_count'] = 0;
        }

        return $data;
    }
}
