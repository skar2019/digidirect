<?php

namespace Digidirect\ParticularAudienceAPI\Block\Widget;


class TestPA extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    protected $_template = 'Digidirect_ParticularAudienceAPI::widget/test-pa.phtml';
  
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,    
        array $data = []
    ) {        
        parent::__construct($context, $data);
    }
}