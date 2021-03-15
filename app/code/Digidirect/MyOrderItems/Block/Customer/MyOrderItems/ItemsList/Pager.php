<?php

namespace Digidirect\MyOrderItems\Block\Customer\MyOrderItems\ItemsList;

use Magento\Framework\View\Element\Template;
use Magento\Theme\Block\Html\Pager as MagentoPager;
use Digidirect\MyOrderItems\Model\UrlHandlerPool;

/**
 * Class Pager
 * @package Digidirect\MyOrderItems\Block\Customer\MyOrderItems\ItemsList
 */
class Pager extends MagentoPager
{
    /**
     * @var UrlHandlerPool
     */
    protected $urlHandlerPool;

    /**
     * Pager constructor.
     * @param Template\Context $context
     * @param UrlHandlerPool $urlHandlerPool
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        UrlHandlerPool $urlHandlerPool,
        array $data = []
    )
    {
        $this->urlHandlerPool = $urlHandlerPool;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve page URL by defined parameters
     *
     * @param array $params
     * @return string
     */
    public function getPagerUrl($params = [])
    {
        $urlParams = [];
        $urlParams['_current'] = false;
        $urlParams['_escape'] = true;
        $urlParams['_use_rewrite'] = true;
        $urlParams['_fragment'] = $this->getFragment();
        $urlParams['_query'] = $params;
        $urlParams = array_replace_recursive($this->urlHandlerPool->execute($this->getRequest()), $urlParams);

        return $this->getUrl($this->getPath(), $urlParams);
    }
}
