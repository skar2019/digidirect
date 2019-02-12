<?php
namespace Ewave\NavigationCMSUpgrade\Model\Menu;

use Ewave\CmsUpgrade\Model\Generator as CMSUpgradeGenerator;
use Ewave\CmsUpgrade\Model\DataVersion;
use Ewave\Navigation\Model\ResourceModel\Menu\CollectionFactory;
use Ewave\Navigation\Model\ResourceModel\Menu\Collection;
use Ewave\CmsUpgrade\Model\GeneratorContext;

/**
 * Class Generator
 *
 * @package Ewave\NavigationCMSUpgrade\Model\Menu
 */
class Generator extends CMSUpgradeGenerator
{
    /**
     * @var CollectionFactory
     */
    protected $menuCollectionFactory;

    /**
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $blockFactory;

    /**
     * Generator constructor.
     * @param GeneratorContext $context
     * @param CollectionFactory $collection
     * @param \Magento\Cms\Model\BlockFactory $blockFactory
     */
    public function __construct(
        GeneratorContext $context,
        CollectionFactory $collection,
        \Magento\Cms\Model\BlockFactory $blockFactory
    ) {
        $this->menuCollectionFactory = $collection;
        $this->blockFactory = $blockFactory;
        parent::__construct($context);
    }
    
    /**
     * @param \Ewave\Navigation\Model\ResourceModel\Menu\Collection $collection
     * @return \Magento\Framework\DataObject
     */
    public function processUpgradeScript($collection)
    {
        $fullCollection = $this->menuCollectionFactory->create();

        $idCodeMapping = [];
        foreach ($fullCollection as $item) {
            $idCodeMapping[$item->getId()] = $item->getMenuItemCode();
        }

        $items = [];

        $connection = $collection->getConnection();
        foreach ($collection->getData() as $key => $item) {
            $storesRelation = $connection->select()
                ->from('ewave_navigation_menu_item_store_relation', 'menu_store_id')
                ->where($connection->quoteInto('menu_entity_id = ?', $item['entity_id']));
            $item['menu_store_id'] = $connection->fetchCol($storesRelation);

            $item['parent_menu_item_code'] = $idCodeMapping[$item['parent_menu_item_id']] ?? null;
            unset($item['type_instance']);
            if (isset($item['cms_block_id']) && !empty($item['cms_block_id'])) {
                /**
                 * @var $block \Magento\Cms\Model\Block
                 * @var $cmsBlockModel \Magento\Cms\Model\Block
                 */
                $cmsBlockModel = $this->blockFactory->create();
                $cmsBlockModel->getResource()->load($cmsBlockModel, $item['cms_block_id']);

                $item['cms_block_id'] = $cmsBlockModel->getIdentifier();
            }
            $items[$key] = $item;
        }
        $data = $this->_getUpgradeData();
        $data['items'][] = $items;
        $nextVersion = $this->_getNextModuleVersion();
        $put = $this->putUpgradeFile($data, $nextVersion);
        if ($put) {
            $this->_changeDbVersion($nextVersion);
        }
        return $this->_result;
    }
}
