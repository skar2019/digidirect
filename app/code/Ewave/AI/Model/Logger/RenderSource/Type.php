<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ewave\AI\Model\Logger\RenderSource;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\View\Model\PageLayout\Config\BuilderInterface;

class Type implements OptionSourceInterface
{
    /**
     * Options
     *
     * @var array
     */
    protected $options;

    /**
     * Helper Logger
     *
     * @var \Ewave\AI\Helper\Logger
     */
    protected $_loggerHelper;

    /**
     * Class constructor
     *
     * @param  \Ewave\AI\Helper\Logger $loggerHelper
     */
    public function __construct(
        \Ewave\AI\Helper\Logger $loggerHelper
    ) {
        $this->_loggerHelper = $loggerHelper;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $options = [];

        foreach ($this->_loggerHelper->getRecordTypes() as $k => $v) {
            $options[] = [
                'label' => $v,
                'value' => $k
            ];
        }

        $this->options = $options;

        return $this->options;
    }
}
