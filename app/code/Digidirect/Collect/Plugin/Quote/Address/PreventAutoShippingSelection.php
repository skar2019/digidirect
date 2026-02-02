<?php
namespace Digidirect\Collect\Plugin\Quote\Address;

use Magento\Quote\Model\Quote\Address;

class PreventAutoShippingSelection
{
    public function beforeSetShippingMethod(
        Address $subject,
                $method
    ) {
        // If trying to set standard_standard, check if it's from user selection
        // If not from explicit user action, prevent it
        if ($method === 'standard_standard') {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);

            // Check if this is being set by user action (from shipping information save)
            $isUserAction = false;
            foreach ($backtrace as $trace) {
                if (isset($trace['class']) &&
                    (strpos($trace['class'], 'ShippingInformationManagement') !== false ||
                        strpos($trace['class'], 'TotalsInformationManagement') !== false)) {
                    $isUserAction = true;
                    break;
                }
            }

            // If not user action and it's trying to set standard, prevent it
            if (!$isUserAction) {
                return [null];
            }
        }

        return [$method];
    }
}
