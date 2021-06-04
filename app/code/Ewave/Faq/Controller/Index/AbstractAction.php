<?php
namespace Ewave\Faq\Controller\Index;

use Ewave\Faq\Api\CategoryRepositoryInterface;

/**
 * Class AbstractAction
 * @package Ewave\Faq\Controller\Index
 */
abstract class AbstractAction extends \Magento\Framework\App\Action\Action
{
    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * AbstractAction constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->categoryRepository = $categoryRepository;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Model\AbstractModel|null
     */
    protected function initCategory()
    {
        $type = $this->getRequest()->getParam('faqType');
        if ($type == 'category') {
            $id = $this->getRequest()->getParam('faqId');
            $category = $this->categoryRepository->getById($id);
            if ($category->getId()) {
                return $category;
            }
        }
        return null;
    }

    /**
     * @param \\Magento\Framework\View\Result\Page $resultPage
     * @return void
     */
    protected function processLayoutUpdate($resultPage)
    {
        $category = $this->initCategory();
        if ($category) {
            if ($pageLayout = $category->getLayoutUpdate()) {
                $resultPage->getConfig()->setPageLayout($pageLayout);
            }

            $resultPage->addHandle(['type' => 'EWAVE_FAQ_CATEGORY_' . $category->getId()]);
            if ($layoutUpdate = trim($category->getLayoutUpdateXml())) {
                $resultPage->addUpdate($layoutUpdate);
            }
        }
    }
}
