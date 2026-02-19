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
            $createdAt = $templateVars['order']->getCreatedAt();
            $formatted = '';
            if (!empty($createdAt)) {
                try {
                    $dt = $this->timezone->date($createdAt);
                    $formatted = $dt->format('j M Y, g:i:s a');
                } catch (\Throwable $e) {
                    $this->logger->debug('Error formatting order created date: ' . $e->getMessage());
                    $formatted = '';
                }
            }

            $templateVars['email_order_created_at_formatted'] = $formatted;
        }

        if (isset($templateVars['shipment']) && is_object($templateVars['shipment'])) {
            $shipment = $templateVars['shipment'];
            $tracksHtml = '';
            try {
                $tracks = method_exists($shipment, 'getAllTracks') ? $shipment->getAllTracks() : [];
                foreach ($tracks as $track) {
                    if ($track->getCarrierCode() == 'standard' ||
                        $track->getCarrierCode() == 'express' ||
                        $track->getCarrierCode() == 'intlshippingnz' ||
                        $track->getCarrierCode() == 'intlshipping') {
                        $tracksHtml = $track->getTitle()." - <a href='https://auspost.com.au/mypost/track/details/".$track->getTrackNumber()."'>".$track->getTrackNumber()."</a>";
                    } elseif ($track->getCarrierCode() == 'nextdayship') {
                        $tracksHtml = $track->getTitle()." - <a href='https://www.gopeople.com.au/tracking/?code=".$track->getTrackNumber()."'>".$track->getTrackNumber()."</a>";
                    } else {
                        $tracksHtml = $track->getTitle()." - ".$track->getTrackNumber();
                    }
                }
            } catch (\Throwable $e) {
                $this->logger->debug('Error building tracks_html: ' . $e->getMessage());
            }
            $templateVars['tracks_html'] = $tracksHtml;
        }

        return [$templateVars];
    }

}
