<?php
namespace Ewave\CustomSitemapLinks\Plugin\Sitemap\Model;

use Ewave\CustomSitemapLinks\Helper\Data as SitemapHelper;
use Magento\Framework\DataObject;

class Sitemap
{
    /**
     * @var SitemapHelper
     */
    protected $sitemapHelper;

    /**
     * Sitemap constructor.
     * @param SitemapHelper $sitemapHelper
     */
    public function __construct(
        SitemapHelper $sitemapHelper
    ) {
        $this->sitemapHelper = $sitemapHelper;
    }

    /**
     * @param \Ewave\Utilities\Model\Sitemap $sitemap
     * @return void
     */
    public function beforeGenerateXml(
        \Ewave\Utilities\Model\Sitemap $sitemap
    ) {
        $this->addSitemapItem($sitemap);
    }

    /**
     * @param \Ewave\Utilities\Model\Sitemap $sitemap
     * @return void
     */
    protected function addSitemapItem(\Ewave\Utilities\Model\Sitemap $sitemap)
    {
        $links = $this->sitemapHelper->getCustomLinks($sitemap->getStoreId());
        if (!empty($links)) {
            $sitemap->addSiteMapItem(new DataObject([
                'changefreq' => $this->sitemapHelper->getCustomLinksChangefreq($sitemap->getStoreId()),
                'priority' => $this->sitemapHelper->getCustomLinksPriority($sitemap->getStoreId()),
                'collection' => $this->prepareLinks($links),
            ]));
        }
    }

    /**
     * @param array $links
     * @return array
     */
    protected function prepareLinks(array $links)
    {
        $items = [];
        foreach ($links as $link) {
            $items[] = new DataObject([
                'url' => $link,
            ]);
        }
        return $items;
    }
}
