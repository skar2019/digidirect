<?php
namespace Ewave\CollectAbstractEntity\Model\ResourceModel\AbstractEntity\Collect;

use Ewave\AbstractEntity\Model\ResourceModel\AbstractEntity\FlatCollection as AbstractEntityFlatCollection;
use Ewave\AbstractEntity\Model\ResourceModel\AbstractEntityFlat as AbstractEntityFlatResource;
use Ewave\CollectAbstractEntity\Model\AbstractEntityCollectPlace;

/**
 * Class CollectionFlat
 * @package Ewave\CollectAbstractEntity\Model\ResourceModel\AbstractEntity\Collect
 */
class CollectionFlat extends AbstractEntityFlatCollection
{
    public function _construct()
    {
        $this->_init(AbstractEntityCollectPlace::class, AbstractEntityFlatResource::class);
    }
}
