<?php
declare(strict_types=1);

namespace Ewave\Digi\Model;

use Magento\Eav\Model\Entity\TypeFactory;
use Ewave\AbstractEntity\Model\AbstractEntityRepository;
use Ewave\Digi\Setup\SeoBrandDescriptionEntitySetup;
use \Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\App\RequestInterface;
use \Magento\Framework\UrlInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Asset\GroupedCollection as PageAsset;
use Ewave\Digi\Helper\AbstractAttribute;
use \Zend\Uri\Http as ZendUrlParser;

/**
 * Class SeoBrandDescription
 * @package Ewave\Digi\Model
 */
class SeoBrandDescription
{
    const ASSET_CANONICAL = 'canonical';
    const EXCLUDED_ID_CATEGORIES = [1, 2];

    /**
     * @var bool
     */
    private $isSeoBrnadUse = false;
    /**
     * @var null
     */
    private $seoBrandEntity = null;

    /**
     * @var AbstractEntityRepository
     */
    private $abstractEntityRepository;
    /**
     * @var Registry
     */
    private $coreRegistry;
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var UrlInterface
     */
    private $url;
    /**
     * @var Config
     */
    private $pageConfig;
    /**
     * @var PageAsset
     */
    private $pageAsset;
    /**
     * @var AbstractAttribute
     */
    private $abstractAttributeHelper;
    /**
     * @var ZendUrlParser
     */
    private $zendUrlParser;

    public function __construct(
        AbstractEntityRepository $abstractEntityRepository,
        Registry $coreRegistry,
        RequestInterface $request,
        UrlInterface $url,
        Config $pageConfig,
        PageAsset $pageAsset,
        AbstractAttribute $abstractAttributeHelper,
        ZendUrlParser $zendUrlParser
    ) {
        $this->abstractEntityRepository = $abstractEntityRepository;
        $this->coreRegistry = $coreRegistry;
        $this->request = $request;
        $this->url = $url;
        $this->pageConfig = $pageConfig;
        $this->pageAsset = $pageAsset;
        $this->abstractAttributeHelper = $abstractAttributeHelper;
        $this->zendUrlParser = $zendUrlParser;
    }

    public function getCurrentOption()
    {
        $optionId = null;
        $currentUrl = $this->getCurrentUrl();
        $urlKey = $currentUrl ? $this->parseUrl($currentUrl) : null;
        $brandId = $urlKey && !empty($urlKey) ? $this->abstractAttributeHelper->getBrandIdByUrlKey($urlKey) : null;
        if ($brandId) {
            $optionId = $brandId;
        }
        return $optionId;
    }

    /**
     * @return bool
     */
    public function isSeoBrandDescriptionUse()
    {
        return $this->isSeoBrnadUse;
    }

    /**
     * @param $seoBrandEntity
     */
    public function setSeoBrandEntity($seoBrandEntity)
    {
        if ($seoBrandEntity && $seoBrandEntity->getId()) {
            $this->seoBrandEntity =  $seoBrandEntity;
            $this->isSeoBrnadUse = true;
        }
    }

    /**
     * @return mixed
     */
    public function getCurrentCategory(): ?CategoryInterface
    {
        $currentCategory = null;

        $result = $this->coreRegistry->registry('current_category');

        if ($result && $result->getId() && $this->checkCategory($result)) {
            $currentCategory = $result;
        }
        return $currentCategory;
    }

    /**
     * @return SeoBrandDescription|null
     */
    public function getSeoBrandEntity()
    {
        $currentCategory = $this->getCurrentCategory();
        if (!$this->seoBrandEntity && $currentCategory) {
            $currentOption = $this->getCurrentOption();
            if ($currentOption && $currentCategory) {
                $entityType = $this->abstractEntityRepository
                    ->getCollection(SeoBrandDescriptionEntitySetup::ABSTRACT_ENTITY_NAME)
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('brand', ['eq' =>  $currentOption])
                    ->addAttributeToFilter('catogory', ['eq' =>  $currentCategory->getId()])
                    ->setPage(1, 1);
                $this->setSeoBrandEntity($entityType->getFirstItem());
            }
        }

        return $this->seoBrandEntity;
    }

    /**
     * @param CategoryInterface $category
     * @return bool
     */
    public function checkCategory($category): bool
    {
        return !in_array($category->getId(), self::EXCLUDED_ID_CATEGORIES);
    }

    /**
     * @param SeoBrandDescription $seoBrandEntity
     */
    public function setMetaInformation($seoBrandEntity)
    {
        $currentUrl = $this->getCurrentUrl();
        if ($this->pageConfig && $currentUrl) {
            $this->pageConfig->setMetaTitle($seoBrandEntity->getMetaTitle());
            $this->pageConfig->setDescription($seoBrandEntity->getMetaDescription());
            $this->pageConfig->setKeywords($seoBrandEntity->getMetaKeywords());
            $this->setCanonical($currentUrl);
        }
    }

    /**
     * @return null|string
     */
    private function getCurrentUrl()
    {
        $currentUrl = null;
        $currentUrl = $this->url->getCurrentUrl();
        if (empty($currentUrl) || strpos($currentUrl, '/filters/') !== false) {
            $currentUrl = null;
        }

        return $currentUrl;
    }

    /**
     * @param $url
     */
    private function setCanonical($url)
    {
        if ($url && !empty($url)) {
            foreach ($this->pageAsset->getAll() as $urlKey => $asset) {
                if ($asset->getContentType() == self::ASSET_CANONICAL) {
                    $this->pageAsset->remove($urlKey);
                }
            }

            $this->pageConfig->addRemotePageAsset(
                $url,
                self::ASSET_CANONICAL,
                ['attributes' => ['rel' => self::ASSET_CANONICAL]]
            );
        }
    }

    /**
     * @param string $url
     * @return mixed|string
     */
    private function parseUrl(string $url)
    {
        $lastPath = '';
        $urlPath = $this->zendUrlParser->parse($url)->getPath();
        if ($urlPath && !empty($urlPath)) {
            $pathArr = explode('/', $urlPath);
            $lastPath = array_pop($pathArr);
        }
        return $lastPath;
    }
}