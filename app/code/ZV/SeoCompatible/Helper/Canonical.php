<?php
declare(strict_types=1);

namespace ZV\SeoCompatible\Helper;

use Magento\Cms\Model\Page;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Request\Http;

class Canonical extends AbstractHelper
{
    /**
     * @var Page
     */
    protected $cmsPage;

    /**
     * Canonical constructor.
     * @param Context $context
     * @param Page    $cmsPage
     */
    public function __construct(
        Context $context,
        Page $cmsPage,
        Http $http,
    ) {
        $this->cmsPage = $cmsPage;
        $this->http = $http;
        parent::__construct($context);
    }

    /**
     * This method is used in XML layout.
     * @return string
     */
    public function getCanonicalForAllCmsPages(): string
    {
        if($this->scopeConfig->getValue('catalog/seo/cms_canonical_tag')){
            if ($this->cmsPage->getId()) {
                return $this->createLink(
                    $this->scopeConfig->getValue('web/secure/base_url') . $this->cmsPage->getIdentifier()
                );
            }
            $checkModule = $this->http->getModuleName();
            if($checkModule == 'contact'){
                return $this->createLink(
                    $this->scopeConfig->getValue('web/secure/base_url') . $this->http->getModuleName()
                );
            }
        }
        
        return '';
    }

    /**
     * @param $url
     * @return string
     */
    protected function createLink($url): string
    {
        return '<link rel="canonical" href="' . $url . '" />';

    }
}