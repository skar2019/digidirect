<?php
namespace Ewave\NavigationCMSUpgrade\Model\Processor;

/***
 * Class AbstractEntity
 *
 * @package Ewave\NavigationCMSUpgrade\Model\Processor
 */
abstract class AbstractEntity
{
    /**
     * @var []
     */
    protected $upgradeData;

    /**
     * @var array
     */
    protected $config;

    /**
     * AbstractEntity constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * @return void
     */
    abstract public function upgrade();

    /**
     * @param [] $itemData
     * @return array
     */
    protected function getData($itemData)
    {
        $newData = [];
        foreach ($this->config as $entityKey => $entityConfig) {
            $class = null;
            if (isset($entityConfig['class'])) {
                $class = $entityConfig['class'];
            }

            if ($this->issetAndIsArray($entityConfig, 'fields')) {
                foreach ($entityConfig['fields'] as $fieldCode => $field) {
                    $class = $this->issetAndInstanceOf($entityConfig, 'class') ? $entityConfig['class'] : $class;
                    if (!$class) {
                        continue;
                    }

                    $callback = $field['callback'] ?? null;
                    $newData[$fieldCode] = $callback ? $class->$callback($itemData)
                        : $class->getData($itemData, $fieldCode);
                }
            }
        }
        return $newData;
    }

    /**
     * @param array $array
     * @param string $key
     * @return bool
     */
    abstract protected function issetAndInstanceOf($array, $key);

    /**
     * @param array $array
     * @param string $key
     * @return bool
     */
    protected function issetAndIsArray(array $array, $key)
    {
        return isset($array[$key]) && is_array($array[$key]);
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setUpgradeData(array $data = [])
    {
        $this->upgradeData = $data;
        return $this;
    }
}
