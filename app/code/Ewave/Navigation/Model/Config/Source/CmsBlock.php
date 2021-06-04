<?php
namespace Ewave\Navigation\Model\Config\Source;

use Magento\Cms\Model\ResourceModel\Block\CollectionFactory as CmsBlockCollectionFactory;

class CmsBlock implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var BlockRepositoryInterface
     */
    protected $blockFactory;

    /**
     * CmsBlock constructor.
     * @param CmsBlockCollectionFactory $blockCollection
     */
    public function __construct(CmsBlockCollectionFactory $blockCollection)
    {
        $this->blockFactory = $blockCollection->create();
    }

    /**
     * Get all CMS blocks
     *
     * @return []
     */
    public function toOptionArray()
    {
        return $this->blockFactory->toOptionArray();
    }
}
