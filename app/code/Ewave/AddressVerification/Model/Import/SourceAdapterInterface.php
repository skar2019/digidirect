<?php
namespace Ewave\AddressVerification\Model\Import;

/**
 * Interface SourceAdapterInterface
 * @package Ewave\AddressVerification\Model\Import
 */
interface SourceAdapterInterface
{
    /**
     * @return array
     */
    public function parse();
}
