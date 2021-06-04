<?php
namespace Ewave\CmsUpgrade\Console\Command\Processor;

use Magento\Framework\App\ObjectManager;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractProcessor
 * @package Ewave\CmsUpgrade\Setup\Processor
 */
abstract class AbstractProcessor
{
    const CHECK_UPDATE_DATE_FLAG = '_check_update_date';

    /**
     * Model factory
     */
    protected $_modelFactory;

    /**
     * Field name for Update Time
     */
    protected $_fieldUpdateTime = 'update_time';

    /**
     * @var array
     */
    protected $_upgradeData = [];

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @param array $data
     * @return mixed
     */
    abstract protected function _prepareModel(array $data);

    /**
     * Upgrades data for a module
     *
     * @param bool $checkUpdateDate
     * @return void
     */
    public function upgrade($checkUpdateDate = false)
    {
        foreach ($this->_upgradeData as $data) {
            if ($checkUpdateDate) {
                if (!isset($data[$this->_fieldUpdateTime])) {
                    continue;
                }
                $data[self::CHECK_UPDATE_DATE_FLAG] = $checkUpdateDate;
            }

            $model = $this->_prepareModel($data);
            if ($model) {
                try {
                    $model->save();
                } catch (\Exception $e) {
                    $this->getLogger()->warning($e->getMessage());
                }
            }
        }
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setUpgradeData(array $data)
    {
        $this->_upgradeData = $data;
        return $this;
    }

    /**
     * Create model
     *
     * @param array $data
     * @return mixed
     */
    public function createModel(array $data = [])
    {
        if (!empty($data)) {
            return $this->_modelFactory->create(['data' => $data]);
        }
        return $this->_modelFactory->create();
    }

    /**
     * @param array $data
     * @param mixed $model
     * @return bool
     */
    public function isNeedToUpdate($data, $model)
    {
        if (isset($data[self::CHECK_UPDATE_DATE_FLAG]) && $data[self::CHECK_UPDATE_DATE_FLAG]) {
            if (!isset($data[$this->_fieldUpdateTime]) || !$model->getData($this->_fieldUpdateTime)) {
                return false;
            }

            $upgradeTime = strtotime($data[$this->_fieldUpdateTime]);
            $currentTime = strtotime($model->getData($this->_fieldUpdateTime));
            if ($upgradeTime <= $currentTime) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return LoggerInterface
     */
    protected function getLogger()
    {
        if ($this->_logger === null) {
            $this->_logger = ObjectManager::getInstance()->get(LoggerInterface::class);
        }
        return $this->_logger;
    }
}
