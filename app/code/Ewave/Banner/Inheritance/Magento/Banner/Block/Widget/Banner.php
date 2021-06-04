<?php

namespace Ewave\Banner\Inheritance\Magento\Banner\Block\Widget;

use Magento\Banner\Block\Widget\Banner as MagentoBannerBlock;
use Ewave\Banner\Block\Widget\Helper\CustomAttributesHelper;
use Ewave\Banner\Block\Widget\Helper\ImageHelper;
use Ewave\Banner\Block\Widget\Helper\VideoHelper;
use Ewave\Banner\Component\Json;
use Ewave\Banner\Model\CustomerSegment;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Banner\Model\ResourceModel\Banner as BannerRM;

/**
 * Empty class to control inheritance differences between EE and CE
 */
class Banner extends MagentoBannerBlock
{
    const NO_LIFETIME = 'no_lifetime';

    /**
     * @var null|[]
     */
    protected $banners = null;

    /**
     * @var null|string
     */
    protected $bannersJson = null;

    /**
     * @var Json
     */
    protected $jsonComponent;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var []
     */
    protected $callbacks = [];

    /**
     * @var \Closure[]
     */
    protected $customCallbacks = [];

    /**
     * @var array
     */
    protected $rendererConfiguration = [];

    /**
     * @var CustomerSegment
     */
    protected $customerSegment;

    /**
     * @var CustomAttributesHelper
     */
    protected $imageHelper;

    /**
     * @var VideoHelper
     */
    protected $videoHelper;

    /**
     * Banner constructor.
     *
     * @param Context $context
     * @param BannerRM $resource
     * @param Json $jsonComponent
     * @param CustomerSegment $customerSegment
     * @param ImageHelper $imageHelper
     * @param VideoHelper $videoHelper
     * @param ObjectManagerInterface $objectManager
     * @param array $rendererConfiguration
     * @param array $callbacks
     * @param array $data
     */
    public function __construct(
        Context $context,
        BannerRM $resource,
        Json $jsonComponent,
        CustomerSegment $customerSegment,
        ImageHelper $imageHelper,
        VideoHelper $videoHelper,
        ObjectManagerInterface $objectManager,
        array $rendererConfiguration,
        array $callbacks = [],
        array $data = []
    ) {
        $this->videoHelper = $videoHelper;
        $this->imageHelper = $imageHelper;
        $this->customerSegment = $customerSegment;
        $this->rendererConfiguration = $rendererConfiguration;
        $this->callbacks = $callbacks;
        $this->jsonComponent = $jsonComponent;
        $this->objectManager = $objectManager;
        parent::__construct($context, $resource, $data);
    }
}
