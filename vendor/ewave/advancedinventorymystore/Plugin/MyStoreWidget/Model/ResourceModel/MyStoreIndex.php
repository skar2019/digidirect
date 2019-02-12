<?php
namespace Ewave\AdvancedInventoryMyStore\Plugin\MyStoreWidget\Model\ResourceModel;

use Ewave\AbstractEntity\Api\Data\AbstractEntityInterface;
use Ewave\AdvancedInventory\Helper\Config;
use Ewave\MyStoreWidget\Api\Data\MyStoreInterface;
use Ewave\MyStoreWidget\Helper\Config as MyStoreWidgetConfigHelper;
use Ewave\MyStoreWidget\Model\ResourceModel\MyStoreIndex as Subject;
use Magento\Framework\Exception\LocalizedException;

class MyStoreIndex
{
    const ADVANCED_INVENTORY_ENTITY_TYPE = 'advanced_inventory';

    /**
     * @var Config
     */
    protected $advancedInventoryConfigHelper;

    /**
     * @var MyStoreWidgetConfigHelper
     */
    protected $myStoreWidgetConfigHelper;

    /**
     * MyStoreIndex constructor.
     *
     * @param MyStoreWidgetConfigHelper $myStoreWidgetConfigHelper
     * @param Config $advancedInventoryConfigHelper
     */
    public function __construct(
        MyStoreWidgetConfigHelper $myStoreWidgetConfigHelper,
        Config $advancedInventoryConfigHelper
    ) {
        $this->advancedInventoryConfigHelper = $advancedInventoryConfigHelper;
        $this->myStoreWidgetConfigHelper = $myStoreWidgetConfigHelper;
    }

    /**
     * @param Subject $subject
     * @param array $items
     * @return array
     * @throws LocalizedException
     */
    public function afterSearchStores(Subject $subject, $items)
    {
        if (!$this->myStoreWidgetConfigHelper->isSearchTypeTextInput()) {
            return $items;
        }
        $entities = $this->advancedInventoryConfigHelper->getAbstractEntities();
        $firstAiEntityId = (string)reset($entities);
        $success = false;
        foreach ($items as $key => $item) {
            if ($item[AbstractEntityInterface::ATTRIBUTE_SET_ID] === $firstAiEntityId) {
                $items[$key][MyStoreInterface::TYPE] = self::ADVANCED_INVENTORY_ENTITY_TYPE;
                $success = true;
            }
        }
        if (!$success) {
            throw new LocalizedException(__('Store not found'));
        }

        return $items;
    }
}
