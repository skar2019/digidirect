<?php
namespace Ewave\LayeredNavigation\Plugin\Catalog;

class ProductListToolbar
{
    /**
     * @var \Ewave\LayeredNavigation\Helper\Data
     */
    protected $helper;

    /**
     * Url builder Helper
     * @var \Ewave\LayeredNavigation\Helper\UrlBuilder
     */
    protected $urlBuilder;

    /**
     * CatalogToolbarPlugin constructor.
     * @param \Ewave\LayeredNavigation\Helper\Data $helper
     * @param \Ewave\LayeredNavigation\Helper\UrlBuilder $urlBuilder
     */
    public function __construct(
        \Ewave\LayeredNavigation\Helper\Data $helper,
        \Ewave\LayeredNavigation\Helper\UrlBuilder $urlBuilder
    ) {
        $this->helper = $helper;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $subject
     * @param \Closure $closure
     * @param array $params
     * @return mixed
     */
    public function aroundGetPagerUrl(
        \Magento\Catalog\Block\Product\ProductList\Toolbar $subject,
        \Closure $closure,
        $params = []
    ) {
        if ($this->helper->isAjaxEnabled()) {
            $params['isAjax'] = null;
            $params['_'] = null;
        }

        return $closure($params);
    }

    /**
     * Before get url change original arguments - set current to false and modify query
     * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $subject
     * @param string $routeOriginal
     * @param [] $paramsOriginal
     * @return []
     */
    public function beforeGetUrl(
        \Magento\Catalog\Block\Product\ProductList\Toolbar $subject,
        $routeOriginal,
        $paramsOriginal
    ) {
        $request = $subject->getRequest();
        $paramsOriginal['_query'] = $this->urlBuilder->excludeParams($request->getParams());
        $paramsOriginal['_escape'] = false;
        return [$routeOriginal, $paramsOriginal];
    }
}
