<?php

declare(strict_types=1);

namespace Ewave\Digi\Plugin\Magento\Catalog\Block\Category;

use Ewave\Digi\Model\SeoBrandDescription as ModelSeoBrand;
use \Magento\Framework\View\Page\Config;

/**
 * Class View
 * @package Ewave\Digi\Plugin\Magento\Catalog\Block\Category
 */
class View
{
    /**
     * @var ModelSeoBrand
     */
    private $seoBrandDescription;
    /**
     * @var \Magento\Framework\View\Page\Config
     */
    private $pageConfig;

    private $isAlreadySet;

    public function __construct(
        Config $pageConfig,
        ModelSeoBrand $seoBrandDescription
    ) {
        $this->seoBrandDescription = $seoBrandDescription;
        $this->pageConfig = $pageConfig;
    }

    /**
     * @param $subject
     * @param $result
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterSetLayout($subject, $result)
    {
        if (!$this->isAlreadySet) {
            if ($this->seoBrandDescription->isCategoryBrandPage()){
                /**
                 * @var $seoBrandEntity \Ewave\Digi\Model\SeoBrandDescription
                 */
                $seoBrandEntity = $this->seoBrandDescription->getSeoBrandEntity();
                $this->seoBrandDescription->setDefaultMetaInformation();
                if ($seoBrandEntity && $seoBrandEntity->getId()) {
                    $seoBrandEntity->setMetaInformationByEntity($seoBrandEntity);
                }
            }

            $this->isAlreadySet = true;
        }
        return [$result];
    }
}