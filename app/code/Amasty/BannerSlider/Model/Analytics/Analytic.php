<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model\Analytics;

use Amasty\BannerSlider\Api\Data\AnalyticInterface;
use Amasty\BannerSlider\Model\ResourceModel\Analytic as AnalyticResource;
use Magento\Framework\Model\AbstractModel;

class Analytic extends AbstractModel implements AnalyticInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(AnalyticResource::class);
    }

    public function getType(): ?string
    {
        return $this->_getData(AnalyticInterface::TYPE);
    }

    public function setType(string $type): void
    {
        $this->setData(AnalyticInterface::TYPE, $type);
    }

    public function getCounter(): ?int
    {
        return $this->hasData(AnalyticInterface::COUNTER)
            ? (int) $this->_getData(AnalyticInterface::COUNTER)
            : null;
    }

    public function setCounter(int $counter): void
    {
        $this->setData(AnalyticInterface::COUNTER, $counter);
    }

    public function getBannerId(): ?int
    {
        return $this->hasData(AnalyticInterface::BANNER_ID)
            ? (int) $this->_getData(AnalyticInterface::BANNER_ID)
            : null;
    }

    public function setBannerId(int $bannerId): void
    {
        $this->setData(AnalyticInterface::BANNER_ID, $bannerId);
    }

    public function getVersionId(): ?int
    {
        return $this->hasData(AnalyticInterface::VERSION_ID)
            ? (int) $this->_getData(AnalyticInterface::VERSION_ID)
            : null;
    }

    public function setVersionId(int $versionId): void
    {
        $this->setData(AnalyticInterface::VERSION_ID, $versionId);
    }
}
