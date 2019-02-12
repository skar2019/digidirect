<?php
namespace Ewave\ExtendedShippingRates\Controller\Adminhtml\ExtendedShippingRates\Quote;

class Delete extends \Ewave\ExtendedShippingRates\Controller\Adminhtml\ExtendedShippingRates\Quote
{
    /**
     * Delete shipping rule action
     *
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->ruleRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted the rule.'));
                $this->_redirect('ewave_extendedshippingrates/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t delete the rule right now. Please review the log and try again.')
                );
                $this->logger->critical($e);
                $this->_redirect('ewave_extendedshippingrates/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a rule to delete.'));
        $this->_redirect('ewave_extendedshippingrates/*/');
    }
}
