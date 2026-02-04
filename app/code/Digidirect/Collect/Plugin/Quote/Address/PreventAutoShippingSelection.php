<?php
namespace Digidirect\Collect\Plugin\Quote\Address;

use Magento\Quote\Model\Quote\Address;
use Psr\Log\LoggerInterface;

class PreventAutoShippingSelection
{
    protected $logger;
    private static $allowStandard = false;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function beforeSetShippingMethod(
        Address $subject,
                $method
    ) {
        // Allow standard_standard if it's being set with explicit rates available
        if ($method === 'standard_standard') {
            // Check if there are shipping rates available (means user is selecting)
            $rates = $subject->getAllShippingRates();

            // If we have rates, it means the user is on the shipping method selection step
            if (!empty($rates)) {
                $this->logger->info('ALLOWING standard_standard - user selection');
                self::$allowStandard = true;
                return [$method];
            }

            // If no rates, it's auto-selection, block it
            $this->logger->info('BLOCKED standard_standard - auto selection');
            return [null];
        }

        return [$method];
    }
}
