<?php

namespace Ewave\Navigation\Model\Frontend;

use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Customer Set context generator
 *
 * @api
 * @since 1.3.0
 */
class CustomerSetMaker
{
    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * @var CustomerSetInterface[]
     */
    protected $customerSetContextBySetCode = [];

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var StoreResolver
     */
    protected $storeResolver;

    /**
     * CustomerSetMaker constructor.
     *
     * @param HttpContext $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreResolver $storeResolver
     */
    public function __construct(
        HttpContext $context,
        ObjectManagerInterface $objectManager,
        StoreResolver $storeResolver
    ) {
        $this->storeResolver = $storeResolver;
        $this->httpContext = $context;
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $setCode
     * @return CustomerSetInterface
     */
    public function makeCustomerSet(string $setCode): CustomerSetInterface
    {
        if (!isset($this->customerSetContextBySetCode[$setCode]) ||
            !($this->customerSetContextBySetCode[$setCode] instanceof CustomerSetInterface)
        ) {
            $this->customerSetContextBySetCode[$setCode] = $this->objectManager->create(
                CustomerSetInterface::class,
                [
                    'isLoggedIn' => $this->isLoggedIn(),
                    'setCode' => $setCode,
                    'storeCode' => $this->getStoreCode(),
                ]
            );
        }

        return $this->customerSetContextBySetCode[$setCode];
    }

    /**
     * @return bool
     */
    private function isLoggedIn()
    {
        return $this->httpContext->getValue(CustomerContext::CONTEXT_AUTH) == true;
    }

    /**
     * @return null|string
     */
    private function getStoreCode()
    {
        return $this->storeResolver->getStoreCode();
    }
}
