<?php

namespace Ewave\Banner\Model\Image;

use Ewave\Banner\Model\Image\Uploader as BannerImagesUploader;
use Ewave\Banner\Model\Attributes\TargetLink;
use Ewave\Banner\Model\Attributes\TargetType;
use Magento\Banner\Model\ResourceModel\Banner\CollectionFactory as BannerCollectionFactory;
use Magento\Banner\Model\ResourceModel\Banner\Collection as BannerCollection;

class Cache
{
    /**
     * @var Uploader
     */
    protected $uploader;

    /**
     * @var BannerCollectionFactory
     */
    protected $bannerCollectionFactory;

    /**
     * @var ImageSerializer
     */
    protected $imageSerializer;

    /**
     * Cache constructor.
     *
     * @param Uploader $uploader
     * @param BannerCollectionFactory $collectionFactory
     * @param TargetLink $targetType
     * @param ImageSerializer $imageSerializer
     */
    public function __construct(
        BannerImagesUploader $uploader,
        BannerCollectionFactory $collectionFactory,
        TargetLink $targetType,
        ImageSerializer $imageSerializer
    ) {
        $this->imageSerializer = $imageSerializer;
        $this->uploader = $uploader;
        $this->bannerCollectionFactory = $collectionFactory;
        $this->targetTypeAttribute = $targetType;
    }

    /**
     * Delete old images and make resize
     *
     * @return void
     */
    public function cleanImagesCache()
    {
        /**
         * @var $bannerCollection BannerCollection
         */
        $bannerCollection = $this->bannerCollectionFactory->create();
        $bannerCollection->getSelect()->joinLeft(
            ['ewave_banner' => $bannerCollection->getTable('ewave_banner_attributes')],
            'main_table.banner_id = ewave_banner.banner_id',
            '*'
        );

        foreach ($bannerCollection as $banner) {
            if ($custom = $this->targetTypeAttribute->getBackendAttributeForEdit(
                $banner,
                TargetType\Custom::TARGET_TYPE_REQUEST_CODE
            )
            ) {
                $banner->setData(TargetType\Custom::TARGET_TYPE_REQUEST_CODE, $custom);
            } elseif ($product = $this->targetTypeAttribute->getBackendAttributeForEdit(
                $banner,
                TargetType\Product::TARGET_TYPE_REQUEST_CODE
            )
            ) {
                $banner->setData(TargetType\Product::TARGET_TYPE_REQUEST_CODE, $product);
            } elseif ($category = $this->targetTypeAttribute->getBackendAttributeForEdit(
                $banner,
                TargetType\Category::TARGET_TYPE_REQUEST_CODE
            )
            ) {
                $banner->setData(TargetType\Category::TARGET_TYPE_REQUEST_CODE, $category);
            }

            $images = $banner->getImages();
            if (!$images) {
                continue;
            }

            $images = $this->imageSerializer->unserialize($images);

            if (empty($images)) {
                continue;
            }

            $imagesToSave = [];
            foreach ($images as $code => $image) {
                if (!$image) {
                    continue;
                }
                $originalImage = basename($image);
                $this->uploader->deleteImage($image);
                $uploaded = $this->uploader->makeResize($originalImage, $code);
                $imagesToSave[$code] = $uploaded;
            }
            if (empty($imagesToSave)) {
                continue;
            }
            $banner->setData('images', $this->imageSerializer->serialize($imagesToSave));
            $banner->save();
        }
    }
}
