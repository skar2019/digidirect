<?php
namespace Ewave\Blog\Controller\View;

use Ewave\Blog\Api\CategoryRepositoryInterface;
use Ewave\Blog\Api\Data\PostInterface;
use Ewave\Blog\Helper\Arrow;
use Ewave\Blog\Helper\Data;
use Ewave\Blog\Model\PostRepository;
use Ewave\Blog\Model\UrlModel;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Index extends Action
{
    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var PostRepository
     */
    protected $postRepository;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var UrlModel
     */
    protected $urlModel;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var Arrow
     */
    protected $arrowHelper;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Data $dataHelper
     * @param PostRepository $postRepository
     * @param Registry $registry
     * @param UrlModel $urlModel
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Arrow $arrowHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Data $dataHelper,
        PostRepository $postRepository,
        Registry $registry,
        UrlModel $urlModel,
        CategoryRepositoryInterface $categoryRepository,
        Arrow $arrowHelper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->dataHelper = $dataHelper;
        $this->postRepository = $postRepository;
        $this->registry = $registry;
        $this->urlModel = $urlModel;
        $this->categoryRepository = $categoryRepository;
        $this->arrowHelper = $arrowHelper;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     * @throws NotFoundException
     */
    public function execute()
    {
        $page = $this->resultPageFactory->create();
        $post = $this->initPost();
        $metaTitle = $post->getMetaTitle() ? $post->getMetaTitle() : $post->getTitle();
        $page->getConfig()->getTitle()->set($metaTitle);
        $page->getConfig()->setKeywords($post->getMetaKeywords());
        $page->getConfig()->setDescription($post->getMetaDescription());
        
        $breadcrumbShow = $this->dataHelper->getGeneralSettingsConfig('breadcrumb');
        if ($breadcrumbShow) {
            $this->addBreadCrumbs($page, $post);
        }
        $pageLayout = $this->dataHelper->getGeneralSettingsConfig('post_layout');
        $pageConfig = $page->getConfig();
        $pageConfig->setPageLayout($pageLayout);
        $page->getLayout()->getUpdate();
        return $page;
    }

    /**
     * @param \Magento\Framework\View\Result\Page $page
     * @param \Ewave\Blog\Model\Post $post
     * @return void
     */
    protected function addBreadCrumbs(\Magento\Framework\View\Result\Page $page, \Ewave\Blog\Model\Post $post)
    {
        $breadcrumbs = $page->getLayout()->getBlock('breadcrumbs');
        if(!$breadcrumbs) {
            return;
        }
        $breadcrumbs->addCrumb(
            'home',
            [
                'label' => __('Home'),
                'title' => __('Home'),
                'link' => $this->_url->getUrl('')
            ]
        );
        
        $targetCategory = null;
        if ($this->arrowHelper->isCategoryCondition()) {
            $targetCategory = $this->categoryRepository->getById($this->arrowHelper->getParams());
        } elseif (($urlKey = $this->urlModel->getCategoryUrlKey($this->_redirect->getRefererUrl()))
        ) {
            $targetCategory = $this->categoryRepository->getByUrlKey($urlKey);
        }

        if ($targetCategory !== null) {
            if (!$targetCategory->getId()) {
                $targetCategory = $this->getCategoryByPostId($post->getId());
            }
            if ($targetCategory->getId()) {
                $parentCategories = $this->categoryRepository->getParentCategories($targetCategory);
                foreach ($parentCategories as $parentCategory) {
                    $breadcrumbs->addCrumb(
                        'category' . $parentCategory->getId(),
                        [
                            'label' => $parentCategory->getName(),
                            'title' => __($parentCategory->getName()),
                            'link' => $parentCategory->getViewUrl()
                        ]
                    );
                }
            }
        } else {
            $breadcrumbs->addCrumb(
                'ewave_blog',
                [
                    'label' => __('Latest Blog Posts'),
                    'title' => __('Latest Blog Posts'),
                    'link' => $this->urlModel->getBlogListUrl()
                ]
            );
        }
        $breadcrumbs->addCrumb(
            'post',
            [
                'label' => __($post->getTitle()),
                'title' => __($post->getTitle()),
                'link' => ''
            ]
        );
    }

    /**
     * @param int $postId
     * @return \Ewave\Blog\Model\Category
     */
    protected function getCategoryByPostId($postId)
    {
        $categoryId = $this->postRepository->getCategoryId($postId);
        return $this->categoryRepository->getById($categoryId);
    }

    /**
     * @return \Ewave\Blog\Model\Post
     * @throws NotFoundException
     */
    protected function initPost()
    {
        $postId = $this->getRequest()->getParam('post_id');
        $post = $this->postRepository->getById($postId);
        if (!$post->getId() || !$post->isActive()) {
            throw new NotFoundException(__('Page not found.'));
        }
        $this->registry->register(PostInterface::CURRENT_ITEM, $post);
        $this->postRepository->updateView($post);
        return $post;
    }
}
