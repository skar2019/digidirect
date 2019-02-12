<?php
namespace Ewave\AbstractAttributes\Controller\Adminhtml\Option;

use Ewave\AbstractAttributes\Api\Data\OptionInterfaceFactory as OptionFactory;
use Ewave\AbstractAttributes\Api\OptionRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var OptionRepositoryInterface
     */
    protected $optionRepository;

    /**
     * @var OptionFactory
     */
    protected $optionFactory;

    /**
     * Delete constructor.
     * @param Context $context
     * @param OptionRepositoryInterface $optionRepository
     * @param OptionFactory $optionFactory
     */
    public function __construct(
        Context $context,
        OptionRepositoryInterface $optionRepository,
        OptionFactory $optionFactory
    ) {
        $this->optionRepository = $optionRepository;
        $this->optionFactory = $optionFactory;

        parent::__construct($context);
    }

    /**
     * Mass delete options
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('option_id');

        try {
            $this->optionRepository->deleteById($id);
            $this->messageManager->addSuccessMessage(__('Option has been deleted.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Check if Is allowed to delete
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ewave_AbstractAttributes::aa_menu_option_delete');
    }
}
