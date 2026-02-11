<?php
namespace Digidirect\EmailCustomerHelp\Plugin;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Psr\Log\LoggerInterface;

class EmailTransportPlugin
{
    private TimezoneInterface $timezone;
    private LoggerInterface $logger;
    public function __construct(TimezoneInterface $timezone, LoggerInterface $logger)
    {
        $this->timezone = $timezone;
        $this->logger = $logger;
    }

    public function beforeSetTemplateVars(
        \Magento\Framework\Mail\Template\TransportBuilder $subject,
        array $templateVars
    ) {
        if (isset($templateVars['order'])) {
            $templateVars['email_order_created_at_formatted'] =
                $this->timezone->formatDate(
                    $templateVars['order']->getCreatedAt(),
                    'EEEE, MMMM d, y',
                    false
                );
        }

        if (isset($templateVars['shipment']) && is_object($templateVars['shipment'])) {
            $shipment = $templateVars['shipment'];
            $tracksHtml = '';
            try {
                $tracks = method_exists($shipment, 'getAllTracks') ? $shipment->getAllTracks() : [];
                foreach ($tracks as $track) {
                    $tracksHtml = $track->getTrackNumber() ?: '';
                }
            } catch (\Throwable $e) {
                $this->logger->debug('Error building tracks_html: ' . $e->getMessage());
            }
            $templateVars['tracks_html'] = $tracksHtml;
        }

        return [$templateVars];
    }

}
