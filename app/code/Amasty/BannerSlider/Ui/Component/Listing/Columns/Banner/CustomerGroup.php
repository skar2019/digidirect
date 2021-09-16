<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Ui\Component\Listing\Columns\Banner;

use Amasty\BannerSlider\Model\OptionSource\CustomerGroup as CustomerGroupSource;
use Magento\Framework\Escaper;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class CustomerGroup extends Column
{
    /**
     * @var CustomerGroupSource
     */
    private $customerGroupSource;

    /**
     * @var Escaper
     */
    private $escaper;

    public function __construct(
        CustomerGroupSource $customerGroupSource,
        Escaper $escaper,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->customerGroupSource = $customerGroupSource;
        $this->escaper = $escaper;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')] = $this->prepareItem($item);
            }
        }

        return $dataSource;
    }

    protected function prepareItem(array $item): string
    {
        $key = $this->getData('name');
        $content = '';

        if (isset($item[$key])) {
            $origCustomerGroups = $item[$key];
        }

        if (!isset($origCustomerGroups)) {
            return '';
        }

        if (!is_array($origCustomerGroups)) {
            $origCustomerGroups = explode(',', $origCustomerGroups);
        }

        $allCustomerGroups = $this->customerGroupSource->toArray();
        if (!array_diff(array_keys($allCustomerGroups), $origCustomerGroups)) {
            return __('All Customer Groups')->render();
        }

        $origCustomerGroups = array_intersect($origCustomerGroups, array_keys($allCustomerGroups));
        foreach ($origCustomerGroups as $customerGroupId) {
            $content .= $this->escaper->escapeHtml($allCustomerGroups[$customerGroupId]) . "<br/>";
        }

        return $content;
    }
}
