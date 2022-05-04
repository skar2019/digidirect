<?php
namespace Digidirect\Catalog\Block;

class MetaTitle extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\Page\Config $pageConfig,
        $data = array()
    ) {
        $this->_scopeConfig = $scopeConfig; 
        $this->_pageConfig = $pageConfig;   

        parent::__construct($context, $data);
    }

     /**
     * Prepare global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->_pageConfig->addBodyClass('advance-sitemap');

        if($this->getSeoTitle())
            $this->_pageConfig->getTitle()->set('Test Meta Title');

        if($this->getMetaKeywords())        
            $this->_pageConfig->setKeywords('Meta Keywords');

        if($this->getMetaDescription())         
            $this->_pageConfig->setDescription('Meta Description');

        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle('Page Heading Title');
        }

        return parent::_prepareLayout();
    }
    
}