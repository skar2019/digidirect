<?php
namespace Ewave\Faq\Plugin\Sitemap\Model;

use Magento\Sitemap\Helper\Data as SitemapHelper;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\DataObject;
use Ewave\Faq\Model\ResourceModel\CategoryRepository;
use Ewave\Faq\Api\Data\CategoryInterface;
use Ewave\Faq\Helper\Data as Helper;

class Sitemap
{
    /**
     * @var SitemapHelper
     */
    protected $sitemapHelper;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * Sitemap constructor.
     *
     * @param SitemapHelper $sitemapHelper
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CategoryRepository $categoryRepository
     * @param Helper $helper
     */
    public function __construct(
        SitemapHelper $sitemapHelper,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CategoryRepository $categoryRepository,
        Helper $helper
    ) {
        $this->sitemapHelper = $sitemapHelper;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->categoryRepository = $categoryRepository;
        $this->helper = $helper;
    }

    /**
     * @param \Ewave\Utilities\Model\Sitemap $sitemap
     * @return void
     */
    public function beforeGenerateXml(\Ewave\Utilities\Model\Sitemap $sitemap)
    {
        $categoryCollection = $this->categoryRepository->getList(
            $this->searchCriteriaBuilder
                ->addFilter(CategoryInterface::STORE_ID, $sitemap->getStoreId())
                ->create()
        )->getItems();

        $faqCollection = [];
        $faq = new DataObject([
            'identifier' => $this->helper->getFaqUrl(),
        ]);
        array_push($faqCollection, $faq);

        $this->addSitemapItem($sitemap, $categoryCollection);
        $this->addSitemapItem($sitemap, $faqCollection);
    }

    /**
     * @param \Ewave\Utilities\Model\Sitemap $sitemap
     * @param array $collection
     * @return void
     */
    protected function addSitemapItem(\Ewave\Utilities\Model\Sitemap $sitemap, $collection)
    {
        $sitemap->addSiteMapItem(new DataObject([
            'changefreq' => $this->sitemapHelper->getCategoryChangefreq($sitemap->getStoreId()),
            'priority' => $this->sitemapHelper->getCategoryPriority($sitemap->getStoreId()),
            'collection' => $this->prepareCollection($collection),
        ]));
    }

    /**
     * @param array $collection
     * @return array
     */
    protected function prepareCollection(array $collection)
    {
        $items = [];
        foreach ($collection as $item) {
            /** @var  $item */
            $items[] = new DataObject([
                'url' => $item->getIdentifier(),
            ]);
        }
        return $items;
    }
}
