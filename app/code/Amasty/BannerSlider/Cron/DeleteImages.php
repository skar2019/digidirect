<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Cron;

class DeleteImages
{
    /**
     * @var \Amasty\BannerSlider\Model\ResourceModel\Banner
     */
    private $banner;

    /**
     * @var \Amasty\BannerSlider\Model\ImageProcessor
     */
    private $imageProcessor;

    public function __construct(
        \Amasty\BannerSlider\Model\ResourceModel\Banner $banner,
        \Amasty\BannerSlider\Model\ImageProcessor $imageProcessor
    ) {
        $this->banner = $banner;
        $this->imageProcessor = $imageProcessor;
    }

    /**
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function execute()
    {
        $this->deleteOldImages();
    }

    /**
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function deleteOldImages()
    {
        $usedImages = array_unique($this->banner->getAllImages());
        $images = $this->imageProcessor->getAllImages();
        $oldImages = array_diff($images, $usedImages);

        foreach ($oldImages as $image) {
            $this->imageProcessor->deleteImage($image);
        }
    }
}
