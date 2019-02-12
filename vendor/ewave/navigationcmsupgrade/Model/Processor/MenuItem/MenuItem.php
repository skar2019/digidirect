<?php
namespace Ewave\NavigationCMSUpgrade\Model\Processor\MenuItem;

use Ewave\NavigationCMSUpgrade\Model\Processor\FieldProcessorInterface;
use Ewave\Navigation\Model\MenuFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class MenuItem
 *
 * @package Ewave\NavigationCMSUpgrade\Model\Processor
 */
class MenuItem extends \Ewave\NavigationCMSUpgrade\Model\Processor\AbstractEntity
{

    /**
     * @var array
     */
    protected $config;

    /**
     * @var MenuFactory
     */
    protected $menuFactory;

    /**
     * MenuItem constructor.
     *
     * @param MenuFactory $menuFactory
     * @param array $config
     */
    public function __construct(
        MenuFactory $menuFactory,
        array $config = []
    ) {
        parent::__construct($config);
        $this->menuFactory = $menuFactory;
    }

    /**
     * Upgrade menu
     *
     * @return void
     */
    public function upgrade()
    {
        foreach ($this->upgradeData as $data) {
            foreach ($data as $item) {
                if (!isset($item['menu_item_code'])) {
                    continue;
                }
                try {
                    $data = $this->getData($item);
                    /**
                     * @var $model \Ewave\Navigation\Model\Menu
                     */
                    $model = $this->menuFactory->create();
                    $model->setData($data);
                    $model->getResource()->save($model);
                } catch (LocalizedException $e) {
                    continue;
                }
            }
        }
    }

    /**
     * @param [] $array
     * @param string $key
     * @return bool
     */
    protected function issetAndInstanceOf($array, $key)
    {
        return isset($array[$key]) && ($array[$key] instanceof FieldProcessorInterface);
    }
}
