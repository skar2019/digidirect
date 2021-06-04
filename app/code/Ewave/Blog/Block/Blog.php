<?php

namespace Ewave\Blog\Block;

use Ewave\Blog\Api\CommentRepositoryInterface;
use Ewave\Blog\Api\Data\PostInterface;
use Ewave\Blog\Api\PostRepositoryInterface;
use Ewave\Blog\Helper\Arrow;
use Ewave\Blog\Helper\Category as CategoryHelper;
use Ewave\Blog\Helper\Data;
use Ewave\Blog\Model\Config\Provider\PageListType;
use Ewave\Blog\Model\Config\Provider\Status;
use Ewave\Blog\Model\Post;
use Ewave\Blog\Model\UrlModel;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Blog extends Template
{
    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var PostRepositoryInterface
     */
    protected $postRepository;

    /**
     * @var CommentRepositoryInterface
     */
    protected $commentRepository;

    /**
     * @var FilterProvider
     */
    protected $filterProvider;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var CategoryHelper
     */
    protected $categoryHelper;

    /**
     * @var Arrow
     */
    protected $arrowHelper;

    /**
     * @var UrlModel
     */
    protected $urlModel;

    /**
     * @var bool
     */
    protected $_isShowCategoryNames = null;

    /**
     * Blog constructor.
     *
     * @param Context $context
     * @param Data $dataHelper
     * @param PostRepositoryInterface $postRepository
     * @param CommentRepositoryInterface $commentRepository
     * @param FilterProvider $filterProvider
     * @param Registry $registry
     * @param CategoryHelper $categoryHelper
     * @param Arrow $arrowHelper
     * @param UrlModel $urlModel
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Data $dataHelper,
        PostRepositoryInterface $postRepository,
        CommentRepositoryInterface $commentRepository,
        FilterProvider $filterProvider,
        Registry $registry,
        CategoryHelper $categoryHelper,
        Arrow $arrowHelper,
        UrlModel $urlModel,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dataHelper = $dataHelper;
        $this->postRepository = $postRepository;
        $this->commentRepository = $commentRepository;
        $this->filterProvider = $filterProvider;
        $this->registry = $registry;
        $this->categoryHelper = $categoryHelper;
        $this->arrowHelper = $arrowHelper;
        $this->urlModel = $urlModel;
    }

    /**
     * @return \Ewave\Blog\Model\ResourceModel\Post\Collection
     */
    public function getCollection()
    {
        if (!$this->hasData('collection')) {
            $collection = $this->prepareCollection();
            $this->setData('collection', $collection);
            if ($this->isShowCategoryNames()) {
                $this->categoryHelper->prepareCategoriesUrls();
            }
        }
        return $this->getData('collection');
    }

    /**
     * @return \Ewave\Blog\Model\ResourceModel\Post\Collection
     */
    protected function prepareCollection()
    {
        $this->arrowHelper->clearCondition();
        $collection = $this->postRepository->getPostList(
            Status::STATUS_ENABLED,
            $this->_storeManager->getStore()->getId(),
            date('Y-m-d')
        );
        $collection->setOrder(
            PostInterface::FIELD_PUBLISH_DATE,
            $this->dataHelper->getGeneralSettingsConfig('post_sorting')
        );
        $collection->setOrder(
            PostInterface::FIELD_ID,
            $this->dataHelper->getGeneralSettingsConfig('post_sorting')
        );
        $search = $this->getRequest()->getParam('s');
        if ($search) {
            $collection->addFieldToFilter(
                ['title', 'short_content', 'content'],
                [
                    ['like' => '%' . $search . '%'],
                    ['like' => '%' . $search . '%'],
                    ['like' => '%' . $search . '%'],
                ]
            );
            $this->arrowHelper->setSearchCondition($search);
        }
        return $collection;
    }

    /**
     * @return bool
     */
    public function isListPageMode()
    {
        return $this->dataHelper->getDisplaySettingsConfig('list_page/show_type') == PageListType::TYPE_LIST;
    }

    /**
     * @param int $postId
     * @return string
     */
    public function getComments($postId)
    {
        return $this->commentRepository->getCountByPostId($postId);
    }

    /**
     * @param Post $post
     * @return mixed
     */
    public function getShortDescription(Post $post)
    {
        return $this->filterProvider->getBlockFilter()->filter($post->getShortContent());
    }

    /**
     * Prepare faq list toolbar
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        /** @var \Magento\Theme\Block\Html\Pager $toolbar */
        $toolbar = $this->getLayout()->getBlock('post_list_pager');
        if ($toolbar) {
            $toolbar->setShowPerPage(false);
            $toolbar->setLimit($this->getPostPerPage());
            $collection = $this->getCollection();
            $toolbar->setCollection($collection);
            $this->setChild('toolbar', $toolbar);
        }
        return $this;
    }

    /**
     * @return number
     */
    protected function getPostPerPage()
    {
        $limit = $this->getLimit();
        if (!$limit) {
            $limit = $this->dataHelper->getPostPerPage();
        }
        return $limit;
    }

    /**
     * @return bool
     */
    public function isCommentsEnabled()
    {
        return $this->dataHelper->getCommentSettingsConfig('type_of_comment');
    }

    /**
     * @return bool
     */
    public function isDisplayTagEnabled()
    {
        return $this->dataHelper->getDisplaySettingsConfig('list_page/display_tags_listing');
    }

    /**
     * @param string $tagName
     * @return string
     */
    public function getTagUrl($tagName)
    {
        return $this->urlModel->getTagUrl($tagName);
    }

    /**
     * @param PostInterface $post
     * @return \Ewave\Blog\Model\Category[]
     */
    public function getCategoriesTitlesAsArray(PostInterface $post)
    {
        $categoryIds = (array)$post->getData('category_id');

        $categories = [];
        foreach ($categoryIds as $categoryId) {
            $category = $this->categoryHelper->getCategory($categoryId);
            if (!empty($category)) {
                $categories[] = $this->categoryHelper->getCategory($categoryId);
            }
        }

        return $categories;
    }

    /**
     * Should post's categories names be included in the view or not.
     *
     * @return bool
     */
    public function isShowCategoryNames()
    {
        if ($this->_isShowCategoryNames === null) {
            $this->_isShowCategoryNames = $this->getData('show_category_name') && $this->isShowCategoryNamesSetting();
        }
        return $this->_isShowCategoryNames;
    }

    /**
     * Return value of the corresponding 'Show category name' config option.
     *
     * @return bool
     */
    public function isShowCategoryNamesSetting()
    {
        return (bool)$this->dataHelper->getDisplaySettingsConfig('list_page/show_category_name');
    }

    /**
     * @return bool|int|null
     */
    public function getCacheLifetime()
    {
        $cacheLifetime = parent::getCacheLifetime();
        if (!$cacheLifetime) {
            $cacheLifetime = 86400;
        }

        return $cacheLifetime;
    }

    /**
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $cacheKey = parent::getCacheKeyInfo();
        $cacheKey['nil'] = $this->getNameInLayout();
        $cacheKey['request_params'] = json_encode($this->getRequest()->getParams());
        $cacheKey['store-code'] = $this->_storeManager->getStore()->getCode();
        return $cacheKey;
    }
}
