<?php

namespace Magestat\SplitOrder\Block\Checkout;

use Magento\Framework\HTTP\Header;
use Magento\Framework\View\Element\Template\Context;
use Magento\Checkout\Model\Session;
use Magento\Sales\Model\Order\Config;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Sales\Api\Data\OrderInterface;
/**
 * Class Success
 * Overriding Magento One page success
 */
class Success extends \Magento\Checkout\Block\Onepage\Success
{
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var OrderInterface
     */
    protected $orderInterface;

    /**
     * @var Header
     */
    protected $httpHeader;

    /**
     * @param Context $context
     * @param Session $checkoutSession
     * @param Config $orderConfig
     * @param HttpContext $httpContext
     * @param Header $httpHeader
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        Config $orderConfig,
        HttpContext $httpContext,
        OrderInterface $orderInterface,
        Header $httpHeader,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $checkoutSession,
            $orderConfig,
            $httpContext,
            $data
        );
        $this->checkoutSession = $checkoutSession;
        $this->orderInterface = $orderInterface;
        $this->httpHeader = $httpHeader;
    }

    /**
     * @return bool|array
     */
    public function getOrderArray()
    {
        $splitOrders = $this->checkoutSession->getOrderIds();
        $this->checkoutSession->unsOrderIds();

        if (empty($splitOrders) || count($splitOrders) <= 1) {
            return false;
        }
        return $splitOrders;
    }

    public function getOrderByIncrementId($id)
    {
        $order = $this->orderInterface->loadByIncrementId($id);
        return $order;
    }

    public function getRealOrderDetail() {
        return $this->checkoutSession->getLastRealOrder();
    }

    /**
     * @return void
     */
    public function getBankDetails($orderId)
    {
        $order = $this->getOrderByIncrementId($orderId);
        $instructions = $order->getPayment()->getMethodInstance()->getInstructions();
        return $this->parseBankInstructions($instructions);
    }

    /**
     * @param string $instructions
     * @return array
     */
    public function parseBankInstructions(string $instructions): array
    {
        $details = [];
        $lines = preg_split('/\r\n|\r|\n/', trim($instructions));

        foreach ($lines as $line) {
            if (strpos($line, ':') !== false) {
                list($label, $value) = explode(':', $line, 2);
                $details[trim($label)] = trim($value);
            }
        }

        return $details;
    }

    /**
     * @return false|int
     */
    public function isMobile()
    {
        $userAgent = $this->httpHeader->getHttpUserAgent();
        return preg_match('/Mobile|Android|iP(hone|od|ad)|IEMobile|BlackBerry|Opera Mini/i', $userAgent);
    }

}
