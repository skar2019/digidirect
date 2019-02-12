<?php
namespace Ewave\AbstractAttributes\Block\Attribute\Option;

use Ewave\AbstractAttributes\Api\Data\OptionInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class View
 * @package Ewave\AbstractAttributes\Block\Attribute\Option
 */
class View extends \Magento\Framework\View\Element\Template
{
    const CACHE_TAG = 'eaa_attribute_option_view';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Ewave\AbstractAttributes\Helper\Url
     */
    protected $urlHelper;

    /**
     * @var \Ewave\AbstractAttributes\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filterProvider;

    /**
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $blockFactory;

    /**
     * View constructor.
     * @param Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Ewave\AbstractAttributes\Helper\Url $urlHelper
     * @param \Ewave\AbstractAttributes\Helper\Data $helper
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Cms\Model\BlockFactory $blockFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Ewave\AbstractAttributes\Helper\Url $urlHelper,
        \Ewave\AbstractAttributes\Helper\Data $helper,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Cms\Model\BlockFactory $blockFactory,
        array $data
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->urlHelper = $urlHelper;
        $this->helper = $helper;
        $this->filterProvider = $filterProvider;
        $this->blockFactory = $blockFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return OptionInterface
     */
    public function getOption()
    {
        if (!$this->hasData('option')) {
            $this->setData('option', $this->coreRegistry->registry('current_eaa_option'));
        }
        return $this->getData('option');
    }

    /**
     * @return string
     */
    public function getCmsBlockContent()
    {
        $blockId = $this->getOption()->getCmsBlock();
        $html = '';
        if ($blockId) {
            $storeId = $this->_storeManager->getStore()->getId();
            /** @var \Magento\Cms\Model\Block $block */
            $block = $this->blockFactory->create();
            $block->setStoreId($storeId);
            $block->getResource()->load($block, $blockId);
            if ($block->isActive()) {
                $html = $this->filterProvider->getBlockFilter()->setStoreId($storeId)->filter($block->getContent());
            }
        }
        return $html;
    }

    /**
     * @param OptionInterface $option
     * @throws LocalizedException
     * @return void
     */
    protected function _addBreadcrumbs(OptionInterface $option)
    {
        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbsBlock->addCrumb('home', [
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link' => $this->_storeManager->getStore()->getBaseUrl()
            ]);
            $attribute = $option->getAttribute();
            if ($attribute->getListingEnabled()) {
                $breadcrumbsBlock->addCrumb('eaa', [
                    'label' => $attribute->getAttributeLabel(),
                    'title' => $attribute->getAttributeLabel(),
                    'link' => $this->urlHelper->getAttributeUrl($attribute)
                ]);
            }
            $breadcrumbsBlock->addCrumb('eaa_option', [
                'label' => $option->getLabel(),
                'title' => $option->getLabel()
            ]);
        }
    }

    /**
     * Prepare global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $option = $this->getOption();
        if (!$option) {
            return parent::_prepareLayout();
        }

        $this->_addBreadcrumbs($option);
        $this->pageConfig->addBodyClass('eaa-option-' . $option->getOptionId());
        $this->pageConfig->addBodyClass('eaa-' . $option->getAttribute()->getAttributeCode());

        $metaTitle = $option->getMetaTitle();
        if (!$metaTitle) {
            $metaTitle = $option->getLabel();
        }
        $this->pageConfig->getTitle()->set($metaTitle);

        $metaDesc = $option->getMetaDesc();
        if (!$metaDesc) {
            $metaDesc = $option->getDescription();
        }
        $this->pageConfig->setDescription($this->helper->prepareMetaDescription($metaDesc));

        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle($this->escapeHtml($option->getLabel()));
        }

        return parent::_prepareLayout();
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $option = $this->getOption();
        if (!$option) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getOption()->getId()];
    }
}
