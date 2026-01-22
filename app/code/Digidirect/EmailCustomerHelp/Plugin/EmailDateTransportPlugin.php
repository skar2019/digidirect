<?php
namespace Digidirect\EmailCustomerHelp\Plugin;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use IntlDateFormatter;

class EmailDateTransportPlugin
{
    private TimezoneInterface $timezone;

    public function __construct(TimezoneInterface $timezone)
    {
        $this->timezone = $timezone;
    }

    public function beforeSetTemplateVars(
        \Magento\Framework\Mail\Template\TransportBuilder $subject,
        array $templateVars
    ) {
        if (isset($templateVars['invoice'])) {
            $templateVars['email_invoice_created_at_formatted'] =
                $this->timezone->formatDate(
                    $templateVars['invoice']->getCreatedAt(),
                    \IntlDateFormatter::FULL
                );
        }

        return [$templateVars];
    }

}
