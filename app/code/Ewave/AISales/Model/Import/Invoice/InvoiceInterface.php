<?php
namespace Ewave\AISales\Model\Import\Invoice;

use Ewave\AI\Model\Lib\Entity\Import\ImportInterface;

interface InvoiceInterface extends ImportInterface
{
    /**
     * @param string $incrementId
     * @param string $storeId
     * @return bool
     */
    public function delete($incrementId, $storeId);
}
