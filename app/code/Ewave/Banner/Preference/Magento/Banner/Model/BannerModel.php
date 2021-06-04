<?php

namespace Ewave\Banner\Preference\Magento\Banner\Model;

use Ewave\Banner\Model\Image\ImageSerializer;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Model\AbstractModel;
use Ewave\Banner\Inheritance\Magento\Banner\Model\Banner as BannerInheritance;
use Magento\Framework\Exception\LocalizedException;

class BannerModel extends BannerInheritance
{
    /**
     * @return $this
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function beforeSave()
    {
        if ('' == trim($this->getName())) {
            throw new LocalizedException(__('Please enter a name.'));
        }

        $bannerContents = $this->getStoreContents();
        $error = true;
        foreach ($bannerContents as $content) {
            if ('' != trim($content)) {
                $error = false;
                break;
            }
        }

        if ($error) {
            $customAttributes = $this->getData('custom_attributes');
            $images = isset($customAttributes['images'])
                ? $this->getImageSerializer()->unserialize($customAttributes['images'])
                : [];
            $uploadedImages = $this->getData('uploaded_images');
            if ($uploadedImages && !empty($uploadedImages)) {
                return AbstractModel::beforeSave();
            }

            $deletedImages = $this->getData('deleted_images');
            if (!$deletedImages && !empty($images)) {
                return AbstractModel::beforeSave();
            }

            if (!empty($this->getData('uploaded_videos'))) {
                return AbstractModel::beforeSave();
            }

            if (!empty($deletedImages)) {
                foreach ($deletedImages as $image) {
                    if (isset($images[$image])) {
                        unset($images[$image]);
                    }
                }
            } else {
                $images = $this->getData('images');
            }

            if (!empty($images)) {
                return AbstractModel::beforeSave();
            }

            throw new LocalizedException(
                __('Please specify default content for at least one store view or upload an image for the banner.')
            );
        }
        return parent::beforeSave();
    }

    /**
     * @return ImageSerializer
     */
    protected function getImageSerializer()
    {
        return ObjectManager::getInstance()->get(ImageSerializer::class);
    }
}
