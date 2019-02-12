<?php

namespace Ewave\AbstractEntity\Plugin\Sitemap\Model;

use Ewave\AbstractEntity\Api\Data\AbstractEntityInterface;
use Ewave\AbstractEntity\Model\AbstractEntity\Attribute\Source\AddToSitemap;
use Ewave\AbstractEntity\Model\AbstractEntity\Attribute\Source\Status;
use Ewave\AbstractEntity\Model\AbstractEntity\Attribute\Source\VisibleOnFrontend;
use Magento\Sitemap\Helper\Data as SitemapHelper;
use Magento\Framework\DataObject;
use Ewave\AbstractEntity\Api\AbstractEntityRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class Sitemap
 * @package Ewave\AbstractEntity\Plugin\Sitemap\Model
 */
class Sitemap
{
    /**
     * @var SitemapHelper
     */
    protected $sitemapHelper;

    /**
     * @var AbstractEntityRepositoryInterface
     */
    protected $abstractEntityRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * Sitemap constructor.
     * @param SitemapHelper $sitemapHelper
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param AbstractEntityRepositoryInterface $abstractEntityRepository
     */
    public function __construct(
        SitemapHelper $sitemapHelper,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        AbstractEntityRepositoryInterface $abstractEntityRepository
    ) {
        $this->sitemapHelper = $sitemapHelper;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->abstractEntityRepository = $abstractEntityRepository;
    }

    /**
     * @param \Ewave\Utilities\Model\Sitemap $sitemap
     * @return void
     */
    public function beforeGenerateXml(\Ewave\Utilities\Model\Sitemap $sitemap)
    {
        $this->addSitemapItem($sitemap, $this->getAeCollection());
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
            'collection' => $this->prepareCollection($collection)
        ]));
    }

    /**
     * @return AbstractEntityInterface[]
     */
    protected function getAeCollection()
    {
        $defaultAttributes = [
            AbstractEntityInterface::URL_KEY,
            AbstractEntityInterface::STATUS,
            AbstractEntityInterface::VISIBLE_ON_FRONTEND,
            AbstractEntityInterface::ADD_TO_SITEMAP
        ];

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(AbstractEntityInterface::STATUS, Status::STATUS_ENABLED)
            ->addFilter(AbstractEntityInterface::VISIBLE_ON_FRONTEND, VisibleOnFrontend::VISIBLE_ON_FRONTEND_ENABLED)
            ->addFilter(AbstractEntityInterface::ADD_TO_SITEMAP, AddToSitemap::ADD_TO_SITEMAP_ENABLED)
            ->create();
        $entities = $this->abstractEntityRepository->getList(
            $searchCriteria,
            null,
            $defaultAttributes
        )->getItems();

        return $entities;
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
                'url' => $item->getUrlKey(),
                'updated_at' =>$item->getUpdatedAt()
            ]);
        }
        return $items;
    }
}
