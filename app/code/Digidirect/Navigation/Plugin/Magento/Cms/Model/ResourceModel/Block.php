<?php
namespace Digidirect\Navigation\Plugin\Magento\Cms\Model\ResourceModel;

use Digidirect\Navigation\Model\Cache;
use Digidirect\Navigation\Model\ResourceModel\Type\Processor;
use Magento\Cms\Model\ResourceModel\Block as CmsBlockRM;
use Magento\Cms\Model\Block as CmsBlockModel;

class Block
{
    /**
     * @var \Digidirect\Navigation\Model\Cache
     */
    protected $navigationCache;

    /**
     * @var \Digidirect\Navigation\Model\ResourceModel\Type\Processor
     */
    protected $resourceModel;

    /**
     * Block constructor.
     * @param \Digidirect\Navigation\Model\Cache $navigationCache
     * @param \Digidirect\Navigation\Model\ResourceModel\Type\Processor $processor
     */
    public function __construct(
        Cache $navigationCache,
        Processor $processor
    ) {
        $this->navigationCache = $navigationCache;
        $this->resourceModel = $processor;
    }

    /**
     * @param \Magento\Cms\Model\ResourceModel\Block $cmsBlockResource
     * @param \Closure $proceed
     * @param \Magento\Cms\Model\Block $block
     * @return \Magento\Cms\Model\ResourceModel\Block
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(
        CmsBlockRM $cmsBlockResource,
        \Closure $proceed,
        CmsBlockModel $block
    ) {
        $hasDataChanges = $block->hasDataChanges();
        $result = $proceed($block);
        if ($hasDataChanges) {
            $menuItemIds = $this->resourceModel->getMenuIdByTargetEntityId([$block->getId()], 'cms_block_id');
            if (!empty($menuItemIds) && is_array($menuItemIds)) {
                $cacheTags = [];
                foreach ($menuItemIds as $menuItemId) {
                    $cacheTags[] = \Digidirect\Navigation\Model\Menu::CACHE_TAG . '_' . $menuItemId;
                }
                if (!empty($cacheTags)) {
                    $this->navigationCache->execute($cacheTags);
                }
            }
        }
        return $result;
    }
}
