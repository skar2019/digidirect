<?php
namespace Ewave\AISales\Model\Import\Shipment;

use Ewave\AI\Model\Lib\Entity\Import\ImportInterface;

interface ShipmentInterface extends ImportInterface
{
    /**
     * @param string $incrementId
     * @param string $storeId
     * @return bool
     */
    public function delete($incrementId, $storeId);
}
