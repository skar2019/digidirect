<?php
namespace Digidirect\OutOfStockNotif\Model;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Area;

class Email extends \Magento\ProductAlert\Model\Email
{
    const ALLOWED_TYPES = ['stock'];

    /**
     * @var string
     */
    protected $_email;

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->_email = $email;
        return $this;
    }

    /**
     * Retrieve stock block
     *
     * @return \Magento\ProductAlert\Block\Email\Stock
     */
    protected function _getStockBlock()
    {
        if ($this->_stockBlock === null) {
            $this->_stockBlock = $this->_productAlertData->createBlock('Digidirect\OutOfStockNotif\Block\Email\Stock');
        }
        return $this->_stockBlock;
    }

    /**
     * Retrieve stock email template
     *
     * @param int $storeId
     * @return \Magento\ProductAlert\Block\Email\Stock
     */
    protected function _getStockEmailTemplate($storeId = null)
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_EMAIL_STOCK_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Send guest email
     *
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function send()
    {
        if ($this->_website === null || $this->_email === null) {
            return false;
        }

        if (in_array($this->_type, self::ALLOWED_TYPES) && count($this->_stockProducts) == 0) {
            return false;
        }

        if (!$this->_website->getDefaultGroup() || !$this->_website->getDefaultGroup()->getDefaultStore()) {
            return false;
        }

        $store = $this->_website->getDefaultStore();
        $storeId = $store->getId();
        $templateId = $this->_getStockEmailTemplate($storeId);

        if (in_array($this->_type, self::ALLOWED_TYPES) && !$templateId) {
            return false;
        }

        if (!in_array($this->_type, self::ALLOWED_TYPES)) {
            return false;
        }

        $this->_appEmulation->startEnvironmentEmulation($storeId);

        $this->_getStockBlock()
            ->setStore($store)
            ->setEmail($this->_email)
            ->reset();

        foreach ($this->_stockProducts as $product) {
            $this->_getStockBlock()->addProduct($product);
        }

        $block = $this->_getStockBlock();
        $alertGrid = $this->_appState->emulateAreaCode(
            Area::AREA_FRONTEND,
            [$block, 'toHtml']
        );

        $this->_appEmulation->stopEnvironmentEmulation();

        $transport = $this->_transportBuilder->setTemplateIdentifier(
            $templateId
        )->setTemplateOptions(
            [
                'area' => Area::AREA_FRONTEND,
                'store' => $storeId
            ]
        )->setTemplateVars(
            [
                'customerName' => $this->_email,
                'alertGrid' => $alertGrid,
            ]
        )->setFrom(
            $this->_scopeConfig->getValue(
                self::XML_PATH_EMAIL_IDENTITY,
                ScopeInterface::SCOPE_STORE,
                $storeId
            )
        )->addTo(
            $this->_email,
            $this->_email
        )->getTransport();

        $transport->sendMessage();
        return true;
    }
}
