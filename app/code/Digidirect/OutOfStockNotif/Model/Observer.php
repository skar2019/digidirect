<?php
namespace Digidirect\OutOfStockNotif\Model;

use Magento\ProductAlert\Model\Observer as MagentoObserver;
use Magento\Store\Model\ScopeInterface;

class Observer extends MagentoObserver
{
    /**
     * Process guest stock emails
     *
     * @param \Digidirect\OutOfStockNotif\Model\Email $email
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _processGuestStock(\Digidirect\OutOfStockNotif\Model\Email $email)
    {
        $email->setType('stock');
        foreach ($this->_getWebsites() as $website) {
            /* @var $website \Magento\Store\Model\Website */
            if (!$website->getDefaultGroup() || !$website->getDefaultGroup()->getDefaultStore()) {
                continue;
            }

            if (!$this->_scopeConfig->getValue(
                self::XML_PATH_STOCK_ALLOW,
                ScopeInterface::SCOPE_STORE,
                $website->getDefaultGroup()->getDefaultStore()->getId()
            )) {
                continue;
            }

            try {
                $collection = $this->_stockColFactory->create()->addWebsiteFilter(
                    $website->getId()
                )->addStatusFilter(
                    0
                )->addFieldToFilter(
                    'customer_id',
                    0
                )->setOrder('email');
            } catch (\Exception $e) {
                $this->_errors[] = $e->getMessage();
                return $this;
            }

            $previousEmail = null;
            $email->setWebsite($website);
            foreach ($collection as $alert) {
                try {
                    if (!$previousEmail || $previousEmail != $alert->getEmail()) {
                        if ($previousEmail) {
                            $email->send();
                        }

                        if (!$alert->getEmail()) {
                            continue;
                        }

                        $previousEmail = $alert->getEmail();
                        $email->clean();
                        $email->setEmail($alert->getEmail());
                    }

                    $product = $this->productRepository->getById(
                        $alert->getProductId(),
                        false,
                        $website->getDefaultStore()->getId()
                    );

                    if ($this->productSalability->isSalable($product, $website)) {
                        $email->addStockProduct($product);

                        $alert->setSendDate($this->_dateFactory->create()->gmtDate());
                        $alert->setSendCount($alert->getSendCount() + 1);
                        $alert->setStatus(1);
                        $alert->save();
                    }
                } catch (\Exception $e) {
                    $this->_errors[] = $e->getMessage();
                }
            }

            if ($previousEmail) {
                try {
                    $email->send();
                } catch (\Exception $e) {
                    $this->_errors[] = $e->getMessage();
                }
            }
        }

        return $this;
    }

    /**
     * Run process send product alerts
     *
     * @return $this
     */
    public function processGuestStock()
    {
        /* @var $email \Digidirect\OutOfStockNotif\Model\Email */
        $email = $this->_emailFactory->create();
        $this->_processGuestStock($email);
        $this->_sendErrorEmail();
        return $this;
    }
}
