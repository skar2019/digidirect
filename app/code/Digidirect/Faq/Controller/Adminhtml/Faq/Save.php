<?php
namespace Digidirect\Faq\Controller\Adminhtml\Faq;

use Magento\Backend\App\Action;
use Digidirect\Faq\Api\FaqRepositoryInterface;
use Digidirect\Faq\Model\FaqFactory;
use Digidirect\Faq\Model\ResourceModel\FaqRepository;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;

/**
 * Class Save
 * @package Digidirect\Faq\Controller\Adminhtml\Faq
 */
class Save extends Action
{
    /**
     * @var FaqRepositoryInterface
     */
    protected $faqRepository;

    /**
     * @var FaqFactory
     */
    protected $faqFactory;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $typeList;

    /**
     * @var array
     */
    protected $data;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param FaqRepository $faqRepository
     * @param FaqFactory $faqFactory
     * @param \Magento\Framework\App\Cache\TypeListInterface $typeList
     * @param array $data
     */
    public function __construct(
        Action\Context $context,
        FaqRepository $faqRepository,
        FaqFactory $faqFactory,
        \Magento\Framework\App\Cache\TypeListInterface $typeList,
        array $data = []
    ) {
        $this->faqRepository = $faqRepository;
        $this->faqFactory = $faqFactory;
        $this->typeList = $typeList;
        $this->data = $data;
        parent::__construct($context);
    }

    /**
     * Save Menu Item
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {

        $resultRedirect = $this->resultRedirectFactory->create();
        $id = (int)$this->getRequest()->getParam('entity_id', 0);
        $params = $this->getRequest()->getPostValue();

        if ($params) {
            try {
                $faqModel = $this->faqFactory->create();
                if (!empty($id)) {
                    $faqModel = $this->faqRepository->getById($id);
                } else {
                    $params['entity_id'] = null;
                }

                if (!$faqModel->getId() && $id) {
                    throw new LocalizedException(__('This faq item no longer exists.'));
                }

                $faqModel->setData($params);

                $this->_eventManager->dispatch(
                    'faq_prepare_save',
                    ['faqModel' => $faqModel, 'request' => $this->getRequest()]
                );

                $this->faqRepository->save($faqModel);

                $this->messageManager->addSuccessMessage(__('You saved faq item'));

                $this->typeList->invalidate($this->data['invalidate_cache_types'] ?? []);

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $faqModel->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
            }

            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('entity_id')]);
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Check permissions for this action
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Digidirect_Faq::faq_faq_items_save');
    }
}
