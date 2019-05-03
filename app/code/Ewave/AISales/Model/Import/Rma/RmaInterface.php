<?php
namespace Ewave\AISales\Model\Import\Rma;

use Ewave\AI\Model\Lib\Entity\Import\ImportInterface;

interface RmaInterface extends ImportInterface
{
    /**
     * @param string $incrementId
     * @param string $storeId
     * @return bool
     */
    public function delete($incrementId, $storeId);
}
