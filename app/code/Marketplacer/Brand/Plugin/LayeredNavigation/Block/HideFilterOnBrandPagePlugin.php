<?php

namespace Marketplacer\Brand\Plugin\LayeredNavigation\Block;

use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\LayeredNavigation\Block\Navigation;
use Marketplacer\BrandApi\Api\BrandAttributeRetrieverInterface;
use Throwable;

class HideFilterOnBrandPagePlugin
{
    /**
     * @var BrandAttributeRetrieverInterface
     */
    protected $brandAttributeRetriever;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @param RequestInterface $request
     * @param BrandAttributeRetrieverInterface $brandAttributeRetriever
     */
    public function __construct(RequestInterface $request, BrandAttributeRetrieverInterface $brandAttributeRetriever)
    {
        $this->request = $request;
        $this->brandAttributeRetriever = $brandAttributeRetriever;
    }

    /**
     * Hide brand attribute filter in layered navigation on brand page only
     *
     * @param Navigation $itemRepository
     * @param array $resultItems
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterGetFilters(
        Navigation $itemRepository,
        array $resultItems
    ) {
        if ('marketplacer_brand_view' !== $this->request->getFullActionName()) {
            return $resultItems;
        }

        try {
            $brandAttributeCode = $this->brandAttributeRetriever->getAttributeCode();
            $resultItems = array_filter($resultItems, function ($filter) use ($brandAttributeCode) {
                if (!$filter instanceof AbstractFilter
                    || !$filter->getData('attribute_model')
                    || $filter->getAttributeModel()->getAttributeCode() !== $brandAttributeCode) {
                    return $filter;
                }
                return null;
            });
        } catch (Throwable $exception) {
            return $resultItems;
        }

        return $resultItems;
    }
}
