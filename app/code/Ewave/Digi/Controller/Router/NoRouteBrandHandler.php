<?php

namespace Ewave\Digi\Controller\Router;

use \Magento\Framework\App\RequestInterface;
use \Ewave\LayeredNavigation\Helper\Url;

class NoRouteBrandHandler implements \Magento\Framework\App\Router\NoRouteHandlerInterface
{
    const BRAND_FILTER = 'brand';

    protected $_isProcessed = false;

    /**
     * @var \Ewave\Digi\Helper\AbstractAttribute
     */
    protected $aaHelper;

    /**
     * NoRouteBrandHandler constructor.
     * @param \Ewave\Digi\Helper\AbstractAttribute $aaHelper
     */
    public function __construct(
        \Ewave\Digi\Helper\AbstractAttribute $aaHelper
    ) {
        $this->aaHelper = $aaHelper;
    }

    /**
     * @param RequestInterface $request
     * @return bool
     */
    public function process(RequestInterface $request)
    {
        if ($this->_isProcessed) return false;

        /**
         * @var \Magento\Framework\App\Request\Http $request
         */
        $path = $request->getRequestUri();

        $parts = explode('/', $path);

        $possibleBrand = explode('.', array_pop($parts));

        if ($this->isBrand($possibleBrand[0])) {
            $parts[] = Url::FILTERS_DELIMITER;
            $parts[] = self::BRAND_FILTER;
            $parts[] = implode('.', $possibleBrand);
            $request->setPathInfo(implode('/', $parts));
            $this->_isProcessed = true;
            return true;
        }

        return false;
    }

    /**
     * @param string $brand
     * @return bool
     */
    protected function isBrand(string $brand)
    {
        if (!empty($brand)) {
            return (bool) $this->aaHelper->getBrandIdByUrlKey($brand);
        }

        return false;
    }
}
