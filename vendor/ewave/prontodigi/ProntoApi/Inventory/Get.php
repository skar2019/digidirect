<?php

namespace Ewave\ProntoDigi\ProntoApi\Inventory;

use Ewave\Pronto\ProntoApi\RequestBuilderInterface;
use Ewave\Pronto\ProntoApi\ResponseHandlerMultipleInterface;
use Ewave\ProntoDigi\Helper\Config as ConfigHelper;
use Ewave\ProntoDigi\ProntoApi\Constants\InventoryGetRequest as InventoryGetRequestConstants;
use Ewave\ProntoDigi\ProntoApi\ProductGetAbstract;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class Get
 * @package Ewave\ProntoDigi\ProntoApi\Inventory
 */
class Get extends ProductGetAbstract
{
    const PROCESS_CODE = 'pronto_inventory_get';

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * @var ConfigHelper
     */
    protected $configHelper;

    /**
     * @var string
     */
    protected $minDate;

    /**
     * Get constructor.
     * @param TimezoneInterface $timezone
     * @param ConfigHelper $configHelper
     * @param RequestBuilderInterface $requestBuilder
     * @param ResponseHandlerMultipleInterface|null $responseHandler
     * @param null $initParams
     */
    public function __construct(
        TimezoneInterface $timezone,
        ConfigHelper $configHelper,
        RequestBuilderInterface $requestBuilder,
        ResponseHandlerMultipleInterface $responseHandler = null,
        $initParams = null
    ) {
        parent::__construct($requestBuilder, $responseHandler, $initParams);
        $this->timezone = $timezone;
        $this->configHelper = $configHelper;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function process()
    {
        $interval = $this->configHelper->getInventoryDiffLastMinutes();
        $intervalObject = new \DateInterval('PT' . $interval . 'M');
        $timezone = $this->configHelper->getTimezone();
        $timezoneObject = new \DateTimeZone($timezone);
        $this->minDate = $this->timezone->date()
            ->setTimezone($timezoneObject)
            ->sub($intervalObject)
            ->format(InventoryGetRequestConstants::REQUEST_DATE_FORMAT);
        return parent::process();
    }

    /**
     * @return $this|\Ewave\ProntoDigi\ProntoApi\Products\Get
     */
    protected function reInitRunOptions()
    {
        $return = parent::reInitRunOptions();
        $this->setRunOption(InventoryGetRequestConstants::DATE_CHANGE_MIN, $this->minDate);
        return $return;
    }
}
