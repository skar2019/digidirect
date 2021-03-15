<?php

namespace Digidirect\MyOrderItems\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Digidirect\Utilities\Model\System\Config\Backend\DefaultOptionModel;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Config
 * @package Digidirect\IndexerManagement\Helper
 */
class Config extends AbstractHelper
{
    /**
     * Config constants
     */
    const DESCRIPTION = 'description';
    const LINK = 'link';
    const DEFAULT_ITEMS_PER_PAGE = 10;

    /**
     * Constants mapping path
     */
    const XML_PATH_MYORDERITEMS_LINKS = 'digidirect_myorderitems/general/myorderitems_links';

    /**
     * Attribute list path
     */
    const XML_PATH_BACKUP_ATTRIBUTELIST = 'digidirect_myorderitems/general/attributes';

    /**
     * Items per page
     */
    const XML_PATH_ITEMS_PER_PAGE = 'digidirect_myorderitems/general/items_per_page';

    /**
     * @var DefaultOptionModel
     */
    protected $defaultOption;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Config constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param DefaultOptionModel $defaultOption
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        DefaultOptionModel $defaultOption
    ) {
        $this->defaultOption = $defaultOption;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @return array
     */
    public function getLinksMapping()
    {
        $fields = $this->scopeConfig->getValue(
            self::XML_PATH_MYORDERITEMS_LINKS,
            ScopeInterface::SCOPE_STORE
        );

        if (empty($fields)) {
            return [];
        }
        return $this->defaultOption->convertValueToArray($fields);
    }

    /**
     * @return mixed
     */
    public function getItemsPerPage()
    {
        $itemsPerPage = (int)$this->scopeConfig->getValue(
            self::XML_PATH_ITEMS_PER_PAGE,
            ScopeInterface::SCOPE_STORE
        );
        if (!$itemsPerPage) {
            $itemsPerPage = self::DEFAULT_ITEMS_PER_PAGE;
        }
        return $itemsPerPage;
    }

    /**
     * @return array
     */
    public function getLinks()
    {
        $links = [];
        foreach ($this->getLinksMapping() as $configRow) {
            $link = $configRow[Config::LINK . '_column'];
            $description = $configRow[Config::DESCRIPTION . '_column'];
            if (empty($link) || empty($description)) {
                continue;
            }
            $item = [];
            $item[Config::LINK] = $this->storeManager->getStore()->getBaseUrl() . $link;
            $item[Config::DESCRIPTION] = $description;
            $links[] = $item;
        }
        return $links;
    }

    /**
     * @return array
     */
    public function getAttributeList()
    {
        if (empty($this->attributeList)) {
            $value = trim($this->scopeConfig->getValue(
                self::XML_PATH_BACKUP_ATTRIBUTELIST,
                ScopeInterface::SCOPE_STORE
            ));
            $this->attributeList = preg_split('/\,/', $value, 0, PREG_SPLIT_NO_EMPTY);
        }
        return $this->attributeList;
    }
}
