<?php
namespace Digidirect\Sales\Model\Email\Sender;

use Magento\Sales\Model\Order;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\App\ObjectManager;

class OrderSender extends \LatitudeNew\Payment\Model\Order\Email\Sender\OrderSender
{
    /**
     * @var Filesystem|null
     */
    protected $filesystem;

    /**
     * Override prepareTemplate to set correct template for collection orders
     *
     * @param Order $order
     * @return void
     */
    protected function prepareTemplate(Order $order)
    {
        $this->writeDebugLog('========================================');
        $this->writeDebugLog('Digidirect prepareTemplate - Start');
        $this->writeDebugLog('Order ID: ' . $order->getId());
        $this->writeDebugLog('Order Increment ID: ' . $order->getIncrementId());

        // Call parent's prepareTemplate first (includes LatitudeNew logic)
        parent::prepareTemplate($order);

        try {
            // Get shipping method
            $shippingMethod = $order->getShippingMethod();

            $this->writeDebugLog('Shipping Method: ' . ($shippingMethod ?: 'NULL'));
            $this->writeDebugLog('Payment Method: ' . $order->getPayment()->getMethod());
            $this->writeDebugLog('Template ID (after parent): ' . $this->templateContainer->getTemplateId());

            // Override template for collection orders
            // This runs AFTER parent, so it takes precedence
            if ($shippingMethod === 'collect_collect') {
                $this->writeDebugLog('CONDITION MATCHED: Collection order detected');
                $this->writeDebugLog('Setting template ID to: 69');

                $this->templateContainer->setTemplateId(69);

                $this->writeDebugLog('Template ID (after set): ' . $this->templateContainer->getTemplateId());
            } else {
                $this->writeDebugLog('Condition NOT matched - using default template');
                $this->writeDebugLog('Expected shipping method: collect_collect');
                $this->writeDebugLog('Actual shipping method: ' . $shippingMethod);
            }

            $this->writeDebugLog('========================================');
        } catch (\Exception $e) {
            $this->writeDebugLog('ERROR: ' . $e->getMessage());
            $this->writeDebugLog('Stack trace: ' . $e->getTraceAsString());
        }
    }

    /**
     * Write to dedicated debug file
     *
     * @param string $message
     * @return void
     */
    protected function writeDebugLog($message)
    {
        try {
            if ($this->filesystem === null) {
                $this->filesystem = ObjectManager::getInstance()->get(Filesystem::class);
            }

            $varDir = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
            $logFile = 'log/digidirect_ordersender_debug.log';
            $varDir->writeFile($logFile, date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL, 'a+');
        } catch (\Exception $e) {
            // Fallback to error_log if file writing fails
            error_log('Digidirect Debug: ' . $message);
        }
    }
}
