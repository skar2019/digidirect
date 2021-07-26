<?php

namespace Digidirect\Pronto\Helper;

class CategoryProcessor extends \Digidirect\AI\Preferences\Model\Import\Product\CategoryProcessor
{
    /**
     * @param null|int $storeId
     * @return array
     */
    public function getCategories($storeId = null)
    {
        $this->initCategories($storeId);
        return $this->categories[$storeId] ?? [];
    }

    /**
     * @param string $categoryPath
     * @param null $storeId
     * @return int
     */
    public function upsertCategory($categoryPath, $storeId = null)
    {
        return parent::upsertCategory($categoryPath, $storeId);
    }

    /**
     * Creates a category.
     *
     * @param string $name
     * @param int $parentId
     * @param string $storeId
     * @return int
     */
    protected function createCategory($name, $parentId, $storeId = null)
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = $this->categoryFactory->create();
        if (!($parentCategory = $this->getCategoryById($parentId))) {
            $parentCategory = $this->categoryFactory->create()->load($parentId);
        }
        $category->setPath($parentCategory->getPath());
        $category->setParentId($parentId);
        $category->setName($name);
        $category->setIsActive(false);
        $category->setIncludeInMenu(false);
        $category->setAttributeSetId($category->getDefaultAttributeSetId());
        if ($storeId !== null) {
            $category->setStoreId($storeId);
        }

        $category->save();
        $this->categoriesCache[$category->getId()] = $category;

        return $category->getId();
    }
}
