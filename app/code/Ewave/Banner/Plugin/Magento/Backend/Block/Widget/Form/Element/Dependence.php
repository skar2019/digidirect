<?php
namespace Ewave\Banner\Plugin\Magento\Backend\Block\Widget\Form\Element;

use Magento\Backend\Block\Widget\Form\Element\Dependence as MagentoElementDependence;
use Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory;
use Ewave\Banner\Model\Widget\BannerRotator\Config as BannerRotatorConfig;

class Dependence
{
    const DEPENDS_VALUE_SEPARATOR = ',';

    /**
     * @var FieldFactory
     */
    protected $fieldFactory;

    /**
     * @var BannerRotatorConfig
     */
    protected $bannerRotatorConfig;

    /**
     * Dependence constructor.
     *
     * @param FieldFactory $fieldFactory
     * @param BannerRotatorConfig $bannerRotatorConfig
     */
    public function __construct(
        FieldFactory $fieldFactory,
        BannerRotatorConfig $bannerRotatorConfig
    ) {
        $this->fieldFactory = $fieldFactory;
        $this->bannerRotatorConfig = $bannerRotatorConfig;
    }

    /**
     * @param MagentoElementDependence $dependence
     * @param \Closure $proceed
     * @param string $fieldName
     * @param string $fieldNameFrom
     * @param string $refField
     * @return \Magento\Backend\Block\Widget\Form\Element\Dependence
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundAddFieldDependence(
        MagentoElementDependence $dependence,
        \Closure $proceed,
        $fieldName,
        $fieldNameFrom,
        $refField
    ) {
        if (!is_object($refField)) {
            $fieldsList = $this->bannerRotatorConfig->getFieldsWithCustomizedDependencies();
            if (array_search($fieldName, $fieldsList) !== false) {
                $dependsValue = explode(static::DEPENDS_VALUE_SEPARATOR, $refField);
                if (count($dependsValue) > 1) {
                    $refField = $this->fieldFactory->create(
                        [
                            'fieldData' => [
                                'value' => (string)$refField,
                                'separator' => static::DEPENDS_VALUE_SEPARATOR,
                            ],
                            'fieldPrefix' => '',
                        ]
                    );
                }
            }
        }
        return $proceed($fieldName, $fieldNameFrom, $refField);
    }
}
