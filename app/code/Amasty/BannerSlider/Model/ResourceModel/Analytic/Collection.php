<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model\ResourceModel\Analytic;

use Amasty\BannerSlider\Api\Data\AnalyticInterface;
use Amasty\BannerSlider\Model\Analytics\Analytic as AnalyticModel;
use Amasty\BannerSlider\Model\ResourceModel\Analytic as AnalyticResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = AnalyticInterface::ID;

    protected function _construct()
    {
        $this->_init(AnalyticModel::class, AnalyticResource::class);
    }
}
