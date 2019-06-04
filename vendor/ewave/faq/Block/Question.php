<?php
namespace Ewave\Faq\Block;

use Magento\Framework\View\Element\Template;

class Question extends Template
{
    /**
     * @var bool
     */
    protected $_isScopePrivate = true;

    /**
     * @return string
     */
    public function getFormAction()
    {
        return $this->_urlBuilder->getUrl('faq/index/question', ['_secure' => $this->_request->isSecure()]);
    }
}
