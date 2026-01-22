<?php

namespace Vendor\ShipmentTracking\Plugin;

use Magento\Sales\Model\Order\Email\Sender\ShipmentSender;

class ShipmentEmailVars
{
    public function beforeSend(ShipmentSender $subject, $shipment, $forceSyncMode = false)
    {
        if ($shipment && $shipment->getAllTracks()) {
            $subject->setTemplateVars(array_merge(
                $subject->getTemplateVars(),
                [
                    'tracks_html' => $shipment->getAllTracksHtml()
                ]
            ));
        }
        return [$shipment, $forceSyncMode];
    }
}
