<?php
namespace Ewave\Blog\Block\Post;

use Ewave\Blog\Api\CategoryRepositoryInterface;
use Ewave\Blog\Api\TagRepositoryInterface;
use Ewave\Blog\Helper\Arrow as ArrowHelper;
use Ewave\Blog\Api\Data\PostInterface;
use Ewave\Blog\Block\Post;
use Ewave\Blog\Model\Config\Provider\Status;
use Ewave\Blog\Api\PostRepositoryInterface;
use Ewave\Blog\Helper\Data;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Cms\Model\Template\FilterProvider;

/**
 * Class Arrow
 */
class Arrow extends Post
{
    /**
     * @var ArrowHelper
     */
    protected $arrowHelper;

    /**
     * Arrow constructor.
     *
     * @param Template\Context $context
     * @param Registry $registry
     * @param Data $helper
     * @param FilterProvider $filterProvider
     * @param PostRepositoryInterface $postRepository
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ArrowHelper $arrowHelper
     * @param TagRepositoryInterface $tagRepository
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        Data $helper,
        FilterProvider $filterProvider,
        PostRepositoryInterface $postRepository,
        CategoryRepositoryInterface $categoryRepository,
        ArrowHelper $arrowHelper,
        TagRepositoryInterface $tagRepository,
        array $data
    ) {
        parent::__construct(
            $context,
            $registry,
            $helper,
            $filterProvider,
            $postRepository,
            $categoryRepository,
            $tagRepository,
            $data
        );
        $this->arrowHelper = $arrowHelper;
    }

    /**
     * @return \Ewave\Blog\Model\Post
     */
    public function getNextPost()
    {
        if (!$this->hasData('blog_next_post')) {
            $this->preparePost();
        }
        return $this->getData('blog_next_post');
    }

    /**
     * @return string
     */
    public function getNextPostLink()
    {
        return $this->getNextPost()->getViewUrl();
    }

    /**
     * @return bool
     */
    public function hasNextPost()
    {
        return !empty($this->getNextPost());
    }

    /**
     * @return \Ewave\Blog\Model\Post
     */
    public function getPreviousPost()
    {
        if (!$this->hasData('blog_previous_post')) {
            $this->preparePost();
        }
        return $this->getData('blog_previous_post');
    }

    /**
     * @return string
     */
    public function getPreviousPostLink()
    {
        return $this->getPreviousPost()->getViewUrl();
    }

    /**
     * @return bool
     */
    public function hasPreviousPost()
    {
        return !empty($this->getPreviousPost());
    }

    /**
     * @return void
     */
    protected function preparePost()
    {
        $collection = $this->repository->getPostList(
            Status::STATUS_ENABLED,
            $this->_storeManager->getStore()->getId()
        );
        $collection->setOrder(
            PostInterface::FIELD_PUBLISH_DATE,
            $this->helper->getGeneralSettingsConfig('post_sorting')
        );
        $collection->setOrder(PostInterface::FIELD_ID, $this->helper->getGeneralSettingsConfig('post_sorting'));
        $this->arrowHelper->applyCondition($collection);
        $previousPost = null;
        $useNext = false;
        
        /** @var \Ewave\Blog\Model\Post $post */
        foreach ($collection as $post) {
            if ($this->getPost()->getId() != $post->getId()) {
                $previousPost = $post;
            }
            if ($useNext === true) {
                $this->setData('blog_next_post', $post);
                break;
            }
            if ($this->getPost()->getId() == $post->getId()) {
                $useNext = true;
                $this->setData('blog_previous_post', $previousPost);

            }
        }
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
        return $cacheKey;
    }
}
