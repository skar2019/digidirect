<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  MageSpark
 * @package   MageSpark\Base
 * @author    MageSpark team <support@magespark.com>
 * @copyright 2020 MageSpark
 */

/**
 * Event observers configuration schema locator
 */
namespace MageSpark\Base\Model\LessToCss\Config;

use Magento\Framework\Config\SchemaLocatorInterface;
use Magento\Framework\Config\Dom\UrnResolver;
use Magento\Framework\Exception\NotFoundException;

/**
 * Class SchemaLocator
 *
 * @package MageSpark\Base\Model\LessToCss\Config
 */
class SchemaLocator implements SchemaLocatorInterface
{
    /**
     * @var UrnResolver
     */
    protected $urnResolver;

    /**
     * SchemaLocator constructor.
     *
     * @param UrnResolver $urnResolver
     */
    public function __construct(UrnResolver $urnResolver)
    {
        $this->urnResolver = $urnResolver;
    }

    /**
     * Get path to merged config schema
     *
     * @return string|null
     * @throws NotFoundException
     */
    public function getSchema()
    {
        return $this->urnResolver->getRealPath('urn:magespark:module:MageSpark_Base:etc/less_to_css.xsd');
    }

    /**
     * Get path to pre file validation schema
     *
     * @return string|null
     * @throws NotFoundException
     */
    public function getPerFileSchema()
    {
        return $this->getSchema();
    }
}
