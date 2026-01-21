<?php
namespace Digidirect\Sales\Plugin\Sales\Model\Order\Email\Sender;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Container\Template;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class OrderSenderPlugin
{
    protected $templateContainer;
    protected $logger;
    protected $filesystem;

    public function __construct(
        Template $templateContainer,
        LoggerInterface $logger,
        Filesystem $filesystem
    ) {
        $this->templateContainer = $templateContainer;
        $this->logger = $logger;
        $this->filesystem = $filesystem;
    }

    /**
     * Before send - this runs BEFORE the email is sent
     */
    public function beforeSend(
        $subject,
        Order $order,
        $forceSyncMode = false
    ) {
        // Write to a separate debug file
        $this->writeDebugLog('========================================');
        $this->writeDebugLog('PLUGIN TRIGGERED: beforeSend');
        $this->writeDebugLog('Time: ' . date('Y-m-d H:i:s'));
        $this->writeDebugLog('Subject Class: ' . get_class($subject));
        $this->writeDebugLog('Order ID: ' . $order->getId());
        $this->writeDebugLog('Order Increment ID: ' . $order->getIncrementId());

        // Also log to system.log
        $this->logger->info('DIGIDIRECT PLUGIN TRIGGERED for order #' . $order->getIncrementId());

        $this->modifyTemplate($order);

        $this->writeDebugLog('========================================');

        return [$order, $forceSyncMode];
    }

    /**
     * After send - verify what template was actually used
     */
    public function afterSend(
        $subject,
        $result,
        Order $order,
        $forceSyncMode = false
    ) {
        $this->writeDebugLog('AFTER SEND - Result: ' . ($result ? 'true' : 'false'));
        $this->writeDebugLog('Template ID used: ' . $this->templateContainer->getTemplateId());

        return $result;
    }

    /**
     * Modify template based on shipping method
     */
    protected function modifyTemplate(Order $order)
    {
        try {
            $shippingMethod = $order->getShippingMethod();
            $currentTemplate = $this->templateContainer->getTemplateId();

            $this->writeDebugLog('Current Template ID: ' . $currentTemplate);
            $this->writeDebugLog('Shipping Method: ' . ($shippingMethod ?: 'NULL'));
            $this->writeDebugLog('Payment Method: ' . $order->getPayment()->getMethod());

            $this->logger->info('Digidirect - Shipping: ' . ($shippingMethod ?: 'NULL') . ', Template: ' . $currentTemplate);

            if ($shippingMethod === 'collect_collect') {
                $this->writeDebugLog('CONDITION MATCHED! Switching from template ' . $currentTemplate . ' to 92');
                $this->logger->info('Digidirect - SWITCHING TO TEMPLATE 92');

                $this->templateContainer->setTemplateId(92);

                $newTemplate = $this->templateContainer->getTemplateId();
                $this->writeDebugLog('New Template ID after set: ' . $newTemplate);
                $this->logger->info('Digidirect - New template set to: ' . $newTemplate);
            } else {
                $this->writeDebugLog('Condition NOT matched. Shipping method is: ' . $shippingMethod);
                $this->writeDebugLog('Expected: collect_collect');
                $this->logger->info('Digidirect - Not collection method, keeping template ' . $currentTemplate);
            }
        } catch (\Exception $e) {
            $this->writeDebugLog('ERROR: ' . $e->getMessage());
            $this->writeDebugLog('Stack: ' . $e->getTraceAsString());
            $this->logger->error('Digidirect Plugin Error: ' . $e->getMessage());
        }
    }

    /**
     * Write to dedicated debug file
     */
    protected function writeDebugLog($message)
    {
        try {
            $varDir = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
            $logFile = 'log/digidirect_ordersender_debug.log';
            $varDir->writeFile($logFile, date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL, 'a+');
        } catch (\Exception $e) {
            // Fallback to error_log if file writing fails
            error_log('Digidirect Debug: ' . $message);
        }
    }
}
