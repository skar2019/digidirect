<?php
namespace Ewave\AISales\Model\Import\CreditMemo;

use Ewave\AI\Model\Lib\Entity\Import\ImportInterface;

interface CreditMemoInterface extends ImportInterface
{
    /**
     * @param string $incrementId
     * @param string $storeId
     * @return bool
     */
    public function delete($incrementId, $storeId);
}
