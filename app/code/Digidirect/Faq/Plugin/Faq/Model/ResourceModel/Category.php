<?php

namespace Digidirect\Faq\Plugin\Faq\Model\ResourceModel;

use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\CmsUrlRewrite\Model\CmsPageUrlPathGenerator;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\Framework\Filter\FilterManager;
use Digidirect\Faq\Helper\Data as FaqHelper;

/**
 * Before save and around delete plugin for \Digidirect\Faq\Model\ResourceModel\Category:
 * - autogenerates url_key if the merchant didn't fill this field
 * - remove all url rewrites for cms page on delete
 */
class Category
{
    /**
     * @var \Magento\CmsUrlRewrite\Model\CmsPageUrlPathGenerator
     */
    protected $cmsPageUrlPathGenerator;

    /**
     * @var UrlPersistInterface
     */
    protected $urlPersist;

    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filterManager;

    /**
     * @var FaqHelper
     */
    protected $_helper;

    /**
     * @param CmsPageUrlPathGenerator $cmsPageUrlPathGenerator
     * @param UrlPersistInterface $urlPersist
     * @param FilterManager $filterManager
     * @param FaqHelper $helper
     */
    public function __construct(
        CmsPageUrlPathGenerator $cmsPageUrlPathGenerator,
        UrlPersistInterface $urlPersist,
        FilterManager $filterManager,
        FaqHelper $helper
    ) {
        $this->cmsPageUrlPathGenerator = $cmsPageUrlPathGenerator;
        $this->urlPersist = $urlPersist;
        $this->filterManager = $filterManager;
        $this->_helper = $helper;
    }

    /**
     * Before save handler
     *
     * @param \Digidirect\Faq\Model\ResourceModel\Category $subject
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSave(
        \Digidirect\Faq\Model\ResourceModel\Category $subject,
        \Magento\Framework\Model\AbstractModel $object
    ) {
        /** @var $object \Digidirect\Faq\Model\Category */
        $urlKey = $object->getData('identifier');
        if ($urlKey === '' || $urlKey === null) {
            $urlKey = $this->generateUrlKey($object);
        }
        $object->setData('identifier', $urlKey);
    }

    /**
     * On delete handler to remove related url rewrites
     *
     * @param \Digidirect\Faq\Model\ResourceModel\Category $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\Model\AbstractModel $category
     * @return \Magento\Cms\Model\ResourceModel\Page
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDelete(
        \Digidirect\Faq\Model\ResourceModel\Category $subject,
        \Closure $proceed,
        \Magento\Framework\Model\AbstractModel $category
    ) {
        $result = $proceed($category);
        if ($category->isDeleted()) {
            $this->urlPersist->deleteByData(
                [
                    UrlRewrite::ENTITY_ID => $category->getId(),
                    UrlRewrite::ENTITY_TYPE => \Digidirect\Faq\Model\Category::URL_REWRITE_ENTITY_TYPE,
                ]
            );
        }

        return $result;
    }

    /**
     * Generate url key based on url_key entered by merchant or page title
     *
     * @param \Digidirect\Faq\Model\ResourceModel\Category $faqCategory
     * @return string
     * @api
     */
    public function generateUrlKey($faqCategory)
    {
        $urlKey = $faqCategory->getIdentifier();
        return $this->filterManager->translitUrl(
            $urlKey === '' || $urlKey === null ? $faqCategory->getTitle() : $urlKey
        );
    }
}
