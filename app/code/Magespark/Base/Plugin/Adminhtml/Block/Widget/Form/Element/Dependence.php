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

namespace MageSpark\Base\Plugin\Adminhtml\Block\Widget\Form\Element;

use Magento\Backend\Block\Widget\Form\Element;
use Magento\Framework\App\ProductMetadataInterface;

/**
 * Class Dependence
 *
 * @package MageSpark\Base\Plugin\Adminhtml\Block\Widget\Form\Element
 */
class Dependence
{
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * Dependence constructor.
     *
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(ProductMetadataInterface $productMetadata)
    {
        $this->productMetadata = $productMetadata;
    }

    /**
     * Compare product metadata version and fieldname
     * @param Element\Dependence $subject
     * @param \Closure $proceed
     * @param $fieldName
     * @param $fieldNameFrom
     * @param $refField
     * @return Element\Dependence
     */
    public function aroundAddFieldDependence(
        Element\Dependence $subject,
        \Closure $proceed,
        $fieldName,
        $fieldNameFrom,
        $refField
    ) {
        if (version_compare($this->productMetadata->getVersion(), '2.2.0', '<')
            && strpos($fieldName, 'groups[][fields]') !== false
        ) {
            return $subject;
        }

        return $proceed($fieldName, $fieldNameFrom, $refField);
    }
}
