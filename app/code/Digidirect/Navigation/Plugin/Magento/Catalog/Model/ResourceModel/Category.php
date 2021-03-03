<?php
namespace Digidirect\Navigation\Plugin\Magento\Catalog\Model\ResourceModel;

class Category
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
        \Digidirect\Navigation\Model\Cache $navigationCache,
        \Digidirect\Navigation\Model\ResourceModel\Type\Processor $processor
    ) {
        $this->navigationCache = $navigationCache;
        $this->resourceModel = $processor;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Category $categoryResource
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\Category $category
     * @return \Magento\Catalog\Model\ResourceModel\Category
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(
        \Magento\Catalog\Model\ResourceModel\Category $categoryResource,
        \Closure $proceed,
        \Magento\Catalog\Model\Category $category
    ) {
        $result = $proceed($category);
        $hasDataChanges = $category->dataHasChangedFor('is_active')
            || $category->dataHasChangedFor(\Magento\Catalog\Model\Category::KEY_INCLUDE_IN_MENU);

        if ($hasDataChanges) {
            $categoriesIds = [$category->getId()];
            $parentCategories = $category->getParentCategories();
            if ($parentCategories) {
                foreach ($parentCategories as $parentCategory) {
                    $categoriesIds[] = $parentCategory->getId();
                }
            }
            $menuItemIds = $this->resourceModel->getMenuIdByTargetEntityId(array_unique($categoriesIds), 'category_id');
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
