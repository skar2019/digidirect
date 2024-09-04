<?php

namespace Digidirect\ParticularAudienceAPI\Block\Widget;


class TestPA extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    protected $variable;
    
    protected $_urlInterface;
    
    protected $_template = 'Digidirect_ParticularAudienceAPI::widget/test-pa.phtml';
  
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,    
        \Magento\Framework\UrlInterface $urlInterface, 
        array $data = []
    ) {        
        $this->_urlInterface = $urlInterface;
        parent::__construct($context, $data);
    }

    public function getCurrentUrl() {
        return rtrim($this->_urlInterface->getCurrentUrl(), '/');
    }
}