<?php

namespace Ewave\AbstractGiftCard\Model\Checks;

use Ewave\AbstractGiftCard\Model\ServiceInterface;
use Magento\Quote\Model\Quote;

/**
 * Specification checks interface
 */
interface SpecificationInterface
{
    /**
     * Check whether service is applicable to quote
     *
     * @param ServiceInterface $service
     * @param \Magento\Quote\Model\Quote $quote
     * @return bool
     */
    public function isApplicable(ServiceInterface $service, Quote $quote);
}
