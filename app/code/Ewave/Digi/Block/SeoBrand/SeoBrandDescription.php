<?php
declare(strict_types=1);

namespace Ewave\Digi\Block\SeoBrand;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Ewave\Digi\Model\SeoBrandDescription as ModelSeoBrand;

/**
 * Class SeoBrandDescription
 * @package Ewave\Digi\Block\SeoBrand
 */
class SeoBrandDescription extends Template
{
    /**
     * @var ModelSeoBrand
     */
    private $seoBrandDescription;

    /**
     * SeoBrandDescription constructor.
     * @param Context $context
     * @param ModelSeoBrand $seoBrandDescription
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ModelSeoBrand $seoBrandDescription,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->seoBrandDescription = $seoBrandDescription;
    }

    /**
     * @return null|string
     */
    public function getSeoData(): ?string
    {
        $result = null;
        $seoBrandEntity = $this->seoBrandDescription->getSeoBrandEntity();

        if ($seoBrandEntity && $seoBrandEntity->getId()) {
            $result = $seoBrandEntity->getCategoryBrandDescription();
        }
        return $result;
    }

    /**
     * void
     */
    public function prepareSeoData()
    {
        $result = null;
        $seoBrandEntity = $this->seoBrandDescription->getSeoBrandEntity();
        $this->seoBrandDescription->setDefaultMetaInformation();

        if ($seoBrandEntity && $seoBrandEntity->getId()) {
            $this->seoBrandDescription->setMetaInformationByEntity($seoBrandEntity);
        }
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->prepareSeoData();
        return $this;
    }
}
