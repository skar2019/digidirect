<?php

namespace Ewave\CmsUpgrade\Console\Command\Processor;

use Magento\Banner\Model\BannerFactory;

class BannerProcessor extends AbstractProcessor
{

    const PK_FIELD = 'name';

    /**
     * @var BannerFactory
     */
    protected $_modelFactory;

    /**
     * BannerProcessor constructor.
     *
     * @param BannerFactory $bannerFactory
     */
    public function __construct(
        BannerFactory $bannerFactory
    ) {
        $this->_modelFactory = $bannerFactory;
    }

    /**
     * @param array $data
     * @return \Magento\Config\Model\Config
     */
    protected function _prepareModel(array $data)
    {
        /** @var \Magento\Banner\Model\Banner $banner */
        $banner = $this->_modelFactory->create();
        $banner->load($data[static::PK_FIELD], static::PK_FIELD);
        $banner->addData($data);
        return $banner;
    }

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
            $retry = false;
            if ($model) {
                try {
                    $model->save();
                } catch (\Exception $e) {
                    /**
                     * Code smells but it updates banner content and settings without images if
                     * something went wrong with images serialization
                     */
                    $className = '\Ewave\Banner\Model\Image\ImageSerializationException';
                    if (class_exists($className) && $e instanceof $className) {
                        $modelData = $model->getData();
                        if (isset($modelData['custom_attributes']['images'])) {
                            unset($modelData['custom_attributes']['images']);
                        }

                        if (isset($modelData['images'])) {
                            unset($modelData['images']);
                        }
                        $retry = true;
                        $model->setData($modelData);
                    }
                    $this->getLogger()->warning($e->getMessage());
                }

                if ($retry) {
                    try {
                        $model->save();
                    } catch (\Throwable $exception) {
                        $this->getLogger()->warning($exception->getMessage());
                    }
                }
            }
        }
    }
}
