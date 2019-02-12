<?php
namespace Ewave\ExtendedShippingRates\Controller\Adminhtml\ExtendedShippingRates\Method;

use \Ewave\ExtendedShippingRates\Model\Carrier\Method;

class Edit extends \Ewave\ExtendedShippingRates\Controller\Adminhtml\ExtendedShippingRates\Method
{
    /**
     * Method edit action
     *
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var \Ewave\ExtendedShippingRates\Model\Carrier\Method $model */
        $model = $this->methodFactory->create();

        if ($id) {
            try {
                $model = $this->methodRepository->getById($id);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
                $this->_redirect('ewave_extendedshippingrates/*');
                return;
            }
        }

        // set entered data if was error when we do save
        $data = $this->_session->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->_session->setData('ewave_current_sMethod_id', $model->getId());
        $this->coreRegistry->register(Method::CURRENT_METHOD, $model);
        $this->_initAction();
        $this->_addBreadcrumb($id ? __('Edit Method') : __('New Method'), $id ? __('Edit Method') : __('New Method'));

        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getEntityId() ? $model->getTitle() : __('New Method')
        );
        $this->_view->renderLayout();
    }
}
