<?php

namespace Ewave\ProntoDigi\ProntoApi\Products\Get\Response;

class CategoryProcessor extends \Ewave\AI\Preferences\Model\Import\Product\CategoryProcessor
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
        try {
            $category->save();
            $this->categoriesCache[$category->getId()] = $category;
        } catch (\Throwable $e) {
            $this->addFailedCategory($category, $e);
        }

        return $category->getId();
    }
}
