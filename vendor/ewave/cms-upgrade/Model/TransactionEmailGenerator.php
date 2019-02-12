<?php
namespace Ewave\CmsUpgrade\Model;

use Magento\Email\Model\ResourceModel\Template\CollectionFactory;

/**
 * Class TransactionEmailGenerator
 * @package Ewave\CmsUpgrade\Model
 */
class TransactionEmailGenerator extends Generator
{

    const PK_ENTITY_FIELD = 'template_id';

    /**
     * @var \Magento\Email\Model\ResourceModel\Template\CollectionFactory
     */
    protected $_templateCollectionFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_storeManager;

    /**
     * @var \Ewave\CmsUpgrade\Helper\MapperWidget
     */
    protected $_helperMapper;

    /**
     * TransactionEmailGenerator constructor.
     * @param GeneratorContext $context
     * @param CollectionFactory $templateCollectionFactory
     */
    public function __construct(
        GeneratorContext $context,
        CollectionFactory $templateCollectionFactory
    ) {
        parent::__construct($context);
        $this->_templateCollectionFactory = $templateCollectionFactory;
    }

    /**
     * @param array $itemsIds
     * @return \Magento\Framework\DataObject
     */
    public function processUpgradeScript(array $itemsIds = [])
    {
        $data = $this->_getUpgradeData();
        $entityCollection = $this->_templateCollectionFactory->create();

        if (!empty($itemsIds)) {
            $entityCollection->addFieldToFilter(self::PK_ENTITY_FIELD, ['in' => $itemsIds]);

            if ($entityCollection->getSize()) {
                foreach ($entityCollection->getItems() as $itemEntity) {
                    $data['items'][] = $this->_fillData($itemEntity);
                }
            }
        }

        $nextVersion = $this->_getNextModuleVersion();
        $put = $this->putUpgradeFile($data, $nextVersion);
        if ($put) {
            $this->_changeDbVersion($nextVersion);
        }
        return $this->_result;
    }
}
