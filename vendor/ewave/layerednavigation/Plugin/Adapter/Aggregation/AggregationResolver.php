<?php
namespace Ewave\LayeredNavigation\Plugin\Adapter\Aggregation;

use Magento\Catalog\Api\AttributeSetFinderInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Search\Request\BucketInterface;
use Magento\Framework\Search\Request\Config;
use Magento\Framework\Search\RequestInterface;

class AggregationResolver
{
    /**
     * @var AttributeSetFinderInterface
     */
    protected $attributeSetFinder;

    /**
     * @var ProductAttributeRepositoryInterface
     */
    protected $productAttributeRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var Config
     */
    protected $config;

    /**
     * AggregationResolver constructor
     *
     * @param AttributeSetFinderInterface $attributeSetFinder
     * @param ProductAttributeRepositoryInterface $productAttributeRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Config $config
     */
    public function __construct(
        AttributeSetFinderInterface $attributeSetFinder,
        ProductAttributeRepositoryInterface $productAttributeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Config $config
    ) {
        $this->attributeSetFinder = $attributeSetFinder;
        $this->productAttributeRepository = $productAttributeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->config = $config;
    }

    /**
     * @param \Magento\CatalogSearch\Model\Adapter\Aggregation\AggregationResolver $subject
     * @param \Closure $proceed
     * @param RequestInterface $request
     * @param array $documentIds
     * @return array
     * @SuppressWarnings("unused")
     */
    public function aroundResolve(
        \Magento\CatalogSearch\Model\Adapter\Aggregation\AggregationResolver $subject,
        $proceed,
        RequestInterface $request,
        array $documentIds
    ) {
        if ($request->getName() != 'catalog_view_container') {
            return $proceed($request, $documentIds);
        }

        $data = $this->config->get($request->getName());

        $bucketKeys = isset($data['aggregations']) ? array_keys($data['aggregations']) : [];
        $attributeCodes = $this->getApplicableAttributeCodes($documentIds);

        $resolvedAggregation = array_filter(
            $request->getAggregation(),
            function ($bucket) use ($attributeCodes, $bucketKeys) {
                /** @var BucketInterface $bucket */
                return in_array($bucket->getField(), $attributeCodes) || in_array($bucket->getName(), $bucketKeys);
            }
        );
        return array_values($resolvedAggregation);
    }

    /**
     * Get applicable attributes
     *
     * @param array $documentIds
     * @return array
     */
    protected function getApplicableAttributeCodes(array $documentIds)
    {
        $attributeSetIds = $this->attributeSetFinder->findAttributeSetIdsByProductIds($documentIds);

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('attribute_set_id', $attributeSetIds, 'in')
            ->create();
        $result = $this->productAttributeRepository->getList($searchCriteria);

        $attributeCodes = [];
        foreach ($result->getItems() as $attribute) {
            $attributeCodes[] = $attribute->getAttributeCode();
        }
        return $attributeCodes;
    }
}
