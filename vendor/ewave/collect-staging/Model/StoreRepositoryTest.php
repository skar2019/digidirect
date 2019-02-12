<?php

namespace Ewave\CollectStaging\Model;

class StoreRepositoryTest implements \Ewave\Collect\Api\CollectPlaceRepositoryInterface
{
protected $stores = [];
    protected $_storeFactory;
    public function __construct(
        \Ewave\CollectStaging\Model\StoretestFactory $storeFactory
    ) {
        $this->_storeFactory = $storeFactory;
    }

    /**
     * Get Store by Id
     *
     * @param string $id
     * @return \Ewave\Collect\Api\Data\CollectPlaceInterface
     */
    public function getById($id)
    {
        $stores = $this->getAll();

        foreach ($stores as $store) {
            if ($store->getId() == $id) {
                return $store;
            }
        }

        return null;
    }

    /**
     * Get All Stores
     *
     * @return \Ewave\Collect\Api\Data\CollectPlaceInterface[]
     */
    public function getAll()
    {
        for ($i=1;$i<=3;$i++) {
            /** @var $store \Ewave\CollectStaging\Model\Storetest*/
            $store = $this->_storeFactory->create();
            $store->setId('store_id_'.$i.$i)
                ->setName('STORE NAME '.$i.$i.$i)
                ->setAddress('1111111111')
                ->setLongitude('144.' . $i . '082568')
                ->setLatitude('-37.' . $i . '282806');
            $this->stores[] = $store;
        }

        return $this->stores;
    }


    /**
     * Get stores list by SKU
     *
     * @param string $sku
     * @param int $qty
     * @return \Ewave\Collect\Api\Data\CollectPlaceInterface[]
     */
    public function getListBySku($sku, $qty = 1)
    {
        return $this->getAll();
    }

    /**
     * Get stores list by SKUs
     *
     * @param array $skus
     * @param int $qty
     * @return \Ewave\Collect\Api\Data\CollectPlaceInterface[]
     */
    public function getListBySkus($skus, $qty = 1)
    {
        return $this->getAll();
    }

    /**
     * Merge Places By Sku - return unique places list
     *
     * @param array $placesBySku
     * @return \Ewave\Collect\Api\Data\CollectPlaceInterface[]
     */
    public function mergePlacesBySku($placesBySku)
    {
        $allPlaces = [];
        foreach ($placesBySku as $sku => $places) {
            foreach ($places as $place) {
                /** @var \Ewave\Collect\Api\Data\CollectPlaceInterface $place * */
                $allPlaces[$place->getId()] = $place;
            }
        }

        return $allPlaces;
    }

    /**
     * get stores list by distance
     *
     * @param string $longitude
     * @param string $latitude
     * @param int $distance
     * @param null|string $sku
     * @return \Ewave\Collect\Api\Data\CollectPlaceInterface[]
     */
    public function getListByDistance($longitude, $latitude, $distance, $sku = null)
    {

    }
}