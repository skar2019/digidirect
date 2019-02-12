<?php
namespace Ewave\LayeredNavigation\Plugin;

class Pager
{
    /**
     * @var \Ewave\LayeredNavigation\Helper\Data
     */
    protected $helper;

    /**
     * PagerPlugin constructor.
     * @param \Ewave\LayeredNavigation\Helper\Data $helper
     */
    public function __construct(\Ewave\LayeredNavigation\Helper\Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Theme\Block\Html\Pager $subject
     * @param \Closure $closure
     * @param array $params
     * @return mixed
     */
    public function aroundGetPagerUrl(\Magento\Theme\Block\Html\Pager $subject, \Closure $closure, $params = [])
    {
        if ($this->helper->isAjaxEnabled()) {
            $params['isAjax'] = null;
            $params['_'] = null;
        }

        return $closure($params);
    }
}
