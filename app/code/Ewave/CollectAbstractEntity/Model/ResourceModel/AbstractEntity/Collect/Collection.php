<?php
namespace Ewave\CollectAbstractEntity\Model\ResourceModel\AbstractEntity\Collect;

use Ewave\AbstractEntity\Model\ResourceModel\AbstractEntity\Collection as AbstractEntityCollection;
use Ewave\AbstractEntity\Model\ResourceModel\AbstractEntity as AbstractEntityResource;
use Ewave\CollectAbstractEntity\Model\AbstractEntityCollectPlace;

/**
 * Class Collection
 * @package Ewave\CollectAbstractEntity\Model\ResourceModel\AbstractEntity\Collect
 */
class Collection extends AbstractEntityCollection
{
    public function _construct()
    {
        $this->_init(AbstractEntityCollectPlace::class, AbstractEntityResource::class);
    }
}
