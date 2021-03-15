<?php

namespace Digidirect\MyOrderItems\Block\Customer\MyOrderItems;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Block\Product\Context;
use Digidirect\MyOrderItems\Helper\Data as DataHelper;
use Digidirect\MyOrderItems\Helper\Config as ConfigHelper;
use Digidirect\MyOrderItems\Model\UrlHandlerPool;

/**
 * Class
 * @package Digidirect\MyOrderItems\Block\MyOrderItems
 */
class AbstractBlock extends Template
{
    /**
     * Request parameters
     */
    const CATEGORY_PARAMETER = 'category_id';
    const ITEM_PARAMETER = 'item_id';
    const RELATED_PRODUCTS_BLOCK_NAME = 'related_products';
    const PAGE_PARAMETER = 'p';

    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var ConfigHelper
     */
    protected $configHelper;

    /**
     * @var UrlHandlerPool
     */
    protected $urlHandlerPool;

    /**
     * AbstractBlock constructor.
     * @param Context $context
     * @param DataHelper $dataHelper
     * @param ConfigHelper $configHelper
     * @param UrlHandlerPool $urlHandlerPool
     * @param array $data
     */
    public function __construct(
        Context $context,
        DataHelper $dataHelper,
        ConfigHelper $configHelper,
        UrlHandlerPool $urlHandlerPool,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        $this->configHelper = $configHelper;
        $this->urlHandlerPool = $urlHandlerPool;
        parent::__construct($context, $data);
    }

    /**
     * @return DataHelper
     */
    public function getDataHelper()
    {
        return $this->dataHelper;
    }

    /**
     * @return ConfigHelper
     */
    public function getConfigHelper()
    {
        return $this->configHelper;
    }

    /**
     * @param array $exclude
     * @return array
     */
    public function collectFilterParams(array $exclude = [])
    {
        return $this->urlHandlerPool->execute($this->getRequest(), $exclude);
    }

    /**
     * @return string
     */
    public function getPageParameter()
    {
        return self::PAGE_PARAMETER;
    }
}
