<?php

namespace Digidirect\Catalog\Block\Category;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Registry;

class CategoryDescription extends Template
{
    protected $registry;
    protected $categoryRepository;

    public function __construct(
        Template\Context $context,
        Registry $registry,
        CategoryRepositoryInterface $categoryRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->categoryRepository = $categoryRepository;
    }

    public function getCategory()
    {
        $category = $this->registry->registry('current_category');
        if ($category) {
            return $category;
        }

        $categoryId = (int) $this->getRequest()->getParam('cat');
        return $categoryId ? $this->categoryRepository->get($categoryId) : null;
    }
}
