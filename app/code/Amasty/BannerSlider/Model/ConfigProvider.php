<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model;

use Magento\Framework\Data\CollectionDataSourceInterface;

class ConfigProvider extends \Amasty\Base\Model\ConfigProviderAbstract implements CollectionDataSourceInterface
{
    /**
     * @var string
     */
    protected $pathPrefix = 'amasty_bannerslider/';
}
