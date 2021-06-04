<?php
namespace Ewave\Faq\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Ewave\Faq\Api\CategoryRepositoryInterface;
use Ewave\Faq\Model\CategoryFactory;
use Ewave\Faq\Model\ResourceModel\CategoryRepository;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;

/**
 * Class Save
 * @package Ewave\Faq\Controller\Adminhtml\Category
 */
class Save extends Action
{
    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

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
     * @param CategoryRepository $categoryRepository
     * @param CategoryFactory $categoryFactory
     * @param \Magento\Framework\App\Cache\TypeListInterface $typeList
     * @param array $data
     */
    public function __construct(
        Action\Context $context,
        CategoryRepository $categoryRepository,
        CategoryFactory $categoryFactory,
        \Magento\Framework\App\Cache\TypeListInterface $typeList,
        array $data = []
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->categoryFactory = $categoryFactory;
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
                $categoryModel = $this->categoryFactory->create();
                if (!empty($id)) {
                    $categoryModel = $this->categoryRepository->getById($id);
                } else {
                    $params['entity_id'] = null;
                }

                if (!$categoryModel->getId() && $id) {
                    throw new LocalizedException(__('This category item no longer exists.'));
                }
                
                $categoryModel->setData($params);

                if (!$categoryModel->validateName()) {
                    throw new LocalizedException(__('Categories with the same name already exists.'));
                }

                $this->_eventManager->dispatch(
                    'faq_category_prepare_save',
                    ['categoryModel' => $categoryModel, 'request' => $this->getRequest()]
                );

                $this->categoryRepository->save($categoryModel);

                $this->messageManager->addSuccessMessage(__('You saved category item'));

                $this->typeList->invalidate($this->data['invalidate_cache_types'] ?? []);

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $categoryModel->getId()]);
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
        return $this->_authorization->isAllowed('Ewave_Faq::faq_category_items_save');
    }
}
