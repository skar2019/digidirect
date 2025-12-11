<?php
namespace Digidirect\OrderEmailDate\Plugin;

use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class OrderSenderPlugin
{
    /**
     * @var TimezoneInterface
     */
    private $timezone;

    public function __construct(TimezoneInterface $timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     * Before send plugin to inject formatted date
     */
    public function beforeSend(OrderSender $subject, $order, $forceSyncMode = false)
    {
        // Format the date as "December 10, 2025"
        $formattedDate = $this->timezone->formatDate(
            $order->getCreatedAt(),
            \IntlDateFormatter::LONG,
            false
        );

        // Inject into template variables
        $order->setData('formatted_order_date', $formattedDate);

        return [$order, $forceSyncMode];
    }
}
