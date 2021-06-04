<?php

namespace Ewave\SEO\Plugin\Magento\Framework;

use Ewave\SEO\Helper\TrailingSlash;
use Magento\Framework\Url;

/**
 * Class UrlInterfacePlugin
 * @package Ewave\SEO\Plugin\Magento\Framework
 */
class UrlInterfacePlugin
{

    /**
     * @var TrailingSlash
     */
    protected $helper;

    /**
     * UrlInterfacePlugin constructor.
     * @param TrailingSlash $helper
     */
    public function __construct(TrailingSlash $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param Url $subject
     * @param mixed $result
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetUrl(
        Url $subject,
        $result
    ) {
        if (!$this->helper->isTrailingSlashEnabled()) {
            $result = rtrim($result, '/');
        }
        return $result;
    }
}
