<?php
namespace Ewave\NavigationCMSUpgrade\Model\Processor\Set;

use Ewave\Navigation\Model\SetFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class MenuItem
 *
 * @package Ewave\NavigationCMSUpgrade\Model\Processor
 */
class Set extends \Ewave\NavigationCMSUpgrade\Model\Processor\AbstractEntity
{
    /**
     * @var SetFactory
     */
    protected $setFactory;

    /**
     * Set constructor.
     *
     * @param SetFactory $setFactory
     * @param array $config
     */
    public function __construct(
        SetFactory $setFactory,
        array $config = []
    ) {
        parent::__construct($config);
        $this->setFactory = $setFactory;
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
                try {
                    $data = $this->getData($item);
                    /**
                     * @var $model \Ewave\Navigation\Model\Menu
                     */
                    $model = $this->setFactory->create();
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
        return isset($array[$key]) && ($array[$key] instanceof Column\FieldProcessorInterface);
    }
}
