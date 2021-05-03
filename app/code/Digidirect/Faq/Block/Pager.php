<?php
namespace Digidirect\Faq\Block;

/**
 * Class Pager
 * @package Digidirect\Faq\Block
 */
class Pager extends \Magento\Theme\Block\Html\Pager
{
    /**
     * @var array
     */
    protected $_params = [];
    
    /**
     * Retrieve page URL by defined parameters
     *
     * @param array $params
     * @return string
     */
    public function getPagerUrl($params = [])
    {
        $urlParams = [];
        $urlParams['_current'] = true;
        $urlParams['_escape'] = true;
        $urlParams['_use_rewrite'] = true;
        $urlParams['_fragment'] = $this->getFragment();

        $params['faqType'] = $this->getFaqType();
        $params['faqId'] = $this->getFaqId();

        $urlParams['_query'] = array_merge($params, $this->_params);

        return $this->getUrl($this->getPath(), $urlParams);
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function setParam($name, $value)
    {
        if (!empty($value)) {
            $this->_params[$name] = $value;
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFaqType()
    {
        return $this->getRequest()->getParam('faqType');
    }

    /**
     * @return mixed
     */
    public function getFaqId()
    {
        return $this->getRequest()->getParam('faqId');
    }
}
