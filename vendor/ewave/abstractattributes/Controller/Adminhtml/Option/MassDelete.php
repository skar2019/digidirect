<?php
namespace Ewave\AbstractAttributes\Controller\Adminhtml\Option;

use Ewave\AbstractAttributes\Api\OptionRepositoryInterface;
use Ewave\AbstractAttributes\Api\Data\OptionInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class MassDelete
 * @package Ewave\AbstractAttributes\Controller\Adminhtml\Option
 */
class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var OptionRepositoryInterface
     */
    protected $optionRepository;

    /**
     * Massactions filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * MassDelete constructor.
     * @param Context $context
     * @param OptionRepositoryInterface $optionRepository
     * @param Filter $filter
     */
    public function __construct(
        Context $context,
        OptionRepositoryInterface $optionRepository,
        Filter $filter
    ) {
        $this->optionRepository = $optionRepository;
        $this->filter = $filter;
        parent::__construct($context);
    }

    /**
     * Mass delete options
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $ids = $this->getRequest()->getParam('selected', []);
        if (empty($ids)) {
            $collection = $this->filter->getCollection($this->optionRepository->getCollection());
            $ids = $collection->getColumnValues(OptionInterface::OPTION_ID);
        }

        $counter = 0;
        foreach ($ids as $id) {
            try {
                $this->optionRepository->deleteById($id);
                $counter++;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been deleted.', $counter)
        );

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
