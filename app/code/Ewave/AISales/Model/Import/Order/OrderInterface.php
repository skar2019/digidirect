<?php
namespace Ewave\AISales\Model\Import\Order;

use Ewave\AI\Model\Lib\Entity\Import\ImportInterface;

interface OrderInterface extends ImportInterface
{
    /**
     * @param string $incrementId
     * @param string $storeId
     * @return bool
     */
    public function delete($incrementId, $storeId);
}
