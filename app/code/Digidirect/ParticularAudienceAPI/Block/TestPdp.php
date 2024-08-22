<?php

namespace Digidirect\ParticularAudienceAPI\Block;

class TestPdp extends \Magento\Framework\View\Element\Template
{
    protected $logger;
    
    protected $_registry;
  
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {        
        $this->logger = $logger;
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }
    
    public function getCurrentProduct(){         
        return $this->_registry->registry('current_product');
    }
}