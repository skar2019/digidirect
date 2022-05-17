<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Ui\DataProvider\Listing\Slider;

use Amasty\BannerSlider\Api\Data\BannerInterface;
use Amasty\BannerSlider\Model\ResourceModel\Slider\Grid\Collection;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Escaper;

class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    const BANNER_FIELD = 'banners';

    /**
     * @var array
     */
    private $mappedFields = [
        'id' => 'main_table.id'
    ];

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var Escaper
     */
    private $escaper;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        \Magento\Framework\UrlInterface $urlBuilder,
        Escaper $escaper,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        $this->urlBuilder = $urlBuilder;
        $this->escaper = $escaper;
    }

    /**
     * @param Filter $filter
     * @return void
     */
    public function addFilter(Filter $filter)
    {
        if (array_key_exists($filter->getField(), $this->mappedFields)) {
            $mappedField = $this->mappedFields[$filter->getField()];
            $filter->setField($mappedField);
        }

        parent::addFilter($filter);
    }

    public function getData(): array
    {
        $data = parent::getData();
        foreach ($data['items'] as &$item) {
            if (isset($item[Collection::BANNER_IDS]) && isset($item[Collection::BANNER_NAMES])) {
                $item[self::BANNER_FIELD] = implode(', ', $this->getBannerLinks($item));
            }
        }

        return $data;
    }

    private function getBannerLinks(array $item): array
    {
        $banners = is_array($item[Collection::BANNER_IDS]) ?: explode(',', $item[Collection::BANNER_IDS]);
        $names = is_array($item[Collection::BANNER_NAMES]) ?: explode(',', $item[Collection::BANNER_NAMES]);
        foreach ($banners as $key => $id) {
            $url = $this->urlBuilder->getUrl('ambannerslider/banner/edit', [BannerInterface::ID => $id]);
            $name = $this->escaper->escapeHtml($names[$key]);
            $bannerLinks[] = sprintf('<a href="%s">%s</a>', $url, $name);
        }

        return $bannerLinks ?? [];
    }
}
