<?php
namespace Ewave\CmsUpgrade\Console\Command\Processor;

/**
 * Class CmsProcessor
 * @package Ewave\CmsUpgrade\Setup\Processor
 */
abstract class CmsProcessor extends AbstractProcessor
{
    /**
     * Prepare model
     *
     * @param array $data
     * @return mixed
     */
    protected function _prepareModel(array $data)
    {
        $collection = $this->createModel()->getCollection()
            ->addFieldToFilter('identifier', $data['identifier']);
        if (isset($data['store_id'])) {
            $collection->addFieldToFilter('store_id', $data['store_id']);
        }
        if ($collection->getSize()) {
            $model = $collection->getFirstItem();
            if (!$this->isNeedToUpdate($data, $model)) {
                return false;
            }
        } else {
            $model = $this->createModel();
        }
        $model->addData($data);
        return $model;
    }
}
