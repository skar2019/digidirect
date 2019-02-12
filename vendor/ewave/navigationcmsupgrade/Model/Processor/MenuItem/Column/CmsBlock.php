<?php
namespace Ewave\NavigationCMSUpgrade\Model\Processor\MenuItem\Column;

use Magento\Cms\Model\BlockFactory;

/**
 * Class CmsBlock
 *
 * @package Ewave\NavigationCMSUpgrade\Model\Processor
 */
class CmsBlock implements FieldProcessorInterface
{
    /**
     * @var BlockFactory
     */
    protected $blockFactory;

    /**
     * CmsBlock constructor.
     *
     * @param BlockFactory $blockFactory
     */
    public function __construct(BlockFactory $blockFactory)
    {
        $this->blockFactory = $blockFactory;
    }

    /**
     * @param [] $data
     * @param string $key
     * @return mixed
     */
    public function getData(array $data, $key)
    {
        return $data[$key] ?? null;
    }

    /**
     * @param [] $data
     * @return int
     */
    public function getCmsBlockIdByIdentifier($data)
    {
        $identifier = $data['cms_block_id'] ?? null;
        if (!$identifier) {
            return null;
        }
        /**
         * @var $blockModel \Magento\Cms\Model\Block
         */
        $blockModel = $this->blockFactory->create();
        $blockModel->getResource()->load($blockModel, $identifier, 'identifier');
        if (($blockModel instanceof \Magento\Cms\Model\Block) && $blockModel->getId()) {
            return $blockModel->getId();
        }
        return null;
    }
}
