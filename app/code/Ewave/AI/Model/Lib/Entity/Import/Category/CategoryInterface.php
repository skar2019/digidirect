<?php
namespace Ewave\AI\Model\Lib\Entity\Import\Category;

use Ewave\AI\Model\Lib\Entity\Import\ImportInterface;

interface CategoryInterface extends ImportInterface
{
    /**
     * @param array $category
     * @return bool
     */
    public function delete(array $category);

    /**
     * @param array $categories
     * @return bool
     */
    public function deleteBunch(array $categories);
}
