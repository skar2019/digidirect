<?php
namespace Digidirect\Collect\Plugin\Quote\Address;

use Magento\Quote\Model\Quote\Address;
use Psr\Log\LoggerInterface;

class PreventAutoShippingSelection
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function beforeSetShippingMethod(
        Address $subject,
                $method
    ) {
        // Allow null - we're explicitly clearing the method
        if ($method === null) {
            return [$method];
        }

        // Block standard_standard if no method is currently selected
        // This prevents auto-selection when switching from C&C to Delivery
        if ($method === 'standard_standard') {
            $currentMethod = $subject->getShippingMethod();

            // If there's no current method, this is auto-selection - block it
            if (empty($currentMethod)) {
                $this->logger->info('BLOCKED standard_standard - no current method (auto-selection)');
                return [null];
            }

            // If current method is already standard_standard, allow it (re-save)
            if ($currentMethod === 'standard_standard') {
                $this->logger->info('ALLOWING standard_standard - re-save of existing method');
                return [$method];
            }

            // If switching FROM another method TO standard, this should be allowed
            // (user explicitly selected it)
            $this->logger->info('ALLOWING standard_standard - switching from ' . $currentMethod);
            return [$method];
        }

        return [$method];
    }

    public function afterSetShippingMethod(
        Address $subject,
                $result,
                $method
    ) {
        $this->logger->info('FINAL shipping method set: ' . var_export($subject->getShippingMethod(), true));
        return $result;
    }
}
