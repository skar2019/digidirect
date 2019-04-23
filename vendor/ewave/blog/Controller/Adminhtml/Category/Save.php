<?php

namespace Ewave\Blog\Controller\Adminhtml\Category;

use Ewave\Blog\Controller\Adminhtml\AbstractSaveAction;
use Ewave\Blog\Model\CacheInvalidator;
use Ewave\Blog\Model\CurrentStoreFetcher;
use Ewave\Blog\Model\UrlModel;
use Magento\Backend\App\Action;
use Ewave\Blog\Api\CategoryRepositoryInterface;
use Ewave\Blog\Model\CategoryFactory;
use Ewave\Blog\Model\CategoryRepository;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;

class Save extends AbstractSaveAction
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var CacheInvalidator
     */
    protected $cacheInvalidator;

    /**
     * @var UrlModel
     */
    protected $urlModel;

    /**
     * Save constructor.
     *
     * @param Action\Context $context
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CategoryFactory $categoryFactory
     * @param UrlModel $urlModel
     * @param CacheInvalidator $cacheInvalidator
     */
    public function __construct(
        Action\Context $context,
        CategoryRepositoryInterface $categoryRepository,
        CategoryFactory $categoryFactory,
        UrlModel $urlModel,
        CacheInvalidator $cacheInvalidator
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->categoryFactory = $categoryFactory;
        $this->cacheInvalidator = $cacheInvalidator;
        $this->urlModel = $urlModel;
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
        $id = $this->getId('category');
        $currentStoreId = $this->getRequest()->getParam(CurrentStoreFetcher::PARAM_STORE);
        $postData = $this->getRequest()->getPostValue();
        if ($postData) {
            try {
                $model = $this->categoryFactory->create();
                if (!empty($id)) {
                    $model = $this->categoryRepository->getById($id);
                } else {
                    $postData['entity_id'] = null;
                }

                if (!$model->getId() && $id) {
                    throw new LocalizedException(__('This category no longer exists.'));
                }
                $this->extractPostData($model, $postData);
                $this->urlModel->prepareUrlKey($model, 'name');
                $this->_eventManager->dispatch(
                    'blog_category_prepare_save',
                    ['model' => $model, 'request' => $this->getRequest()]
                );

                if(!$model->getId()) {
                    $model->setData('is_new_category', true);
                }
                $this->categoryRepository->save($model);

                $this->messageManager->addSuccessMessage(__('You saved category'));

                $this->cacheInvalidator->invalidate();

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [
                            'id' => $model->getId(),
                            CurrentStoreFetcher::PARAM_STORE => $currentStoreId,
                        ]
                    );
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
            }

            return $resultRedirect->setPath(
                '*/*/edit',
                ['id' => $id, CurrentStoreFetcher::PARAM_STORE => $currentStoreId]
            );
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
        return $this->_authorization->isAllowed('Ewave_Blog::blogcat');
    }
}
