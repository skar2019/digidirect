<?php
namespace Ewave\Locator\Model\Config;

use Magento\Framework\Config\SchemaLocatorInterface;
use Magento\Framework\Module\Dir;
use Magento\Framework\Config\Dom\UrnResolver;

/**
 * Class SchemaLocator
 * @package Ewave\Locator\Model\Config
 */
class SchemaLocator implements SchemaLocatorInterface
{
    /** @var UrnResolver */
    protected $urnResolver;

    /**
     * SchemaLocator constructor.
     * @param UrnResolver $urnResolver
     */
    public function __construct(UrnResolver $urnResolver)
    {
        $this->urnResolver = $urnResolver;
    }

    /**
     * Get path to merged config schema
     *
     * @return string
     */
    public function getSchema()
    {
        return $this->urnResolver->getRealPath('urn:ewave:module:Ewave_Locator:etc/locator.xsd');
    }

    /**
     * Get path to pre file validation schema
     *
     * @return string
     */
    public function getPerFileSchema()
    {
        return $this->urnResolver->getRealPath('urn:ewave:module:Ewave_Locator:etc/locator.xsd');
    }
}
