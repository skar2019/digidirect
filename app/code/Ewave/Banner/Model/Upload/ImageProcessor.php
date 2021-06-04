<?php

namespace Ewave\Banner\Model\Upload;

use Magento\Framework\Image\Factory as ImageFactory;
use Magento\Framework\Model\AbstractModel;

class ImageProcessor
{
    /**
     * @var ImageUploader
     */
    protected $imageUploader;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $fileSystemDriver;

    /**
     * @var ImageFactory
     */
    protected $imageFactory;

    /**
     * ImageProcessor constructor.
     * @param ImageUploader $imageUploader
     * @param \Magento\Framework\Filesystem\Io\File $fileSystemDriver
     * @param ImageFactory $imageFactory
     */
    public function __construct(
        \Ewave\Banner\Model\Upload\ImageUploader $imageUploader,
        \Magento\Framework\Filesystem\Io\File $fileSystemDriver,
        ImageFactory $imageFactory
    ) {
        $this->imageUploader = $imageUploader;
        $this->fileSystemDriver = $fileSystemDriver;
        $this->imageFactory = $imageFactory;
    }

    /**
     * @param array|\Zend\Stdlib\Parameters $files
     * @return \string[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveFileToTmpDir($files)
    {
        if ($files instanceof \Zend\Stdlib\Parameters) {
            $files = $files->toArray();
        }
        return $this->imageUploader->saveFileToTmpDir($files[key($files)]);
    }

    /**
     * @param AbstractModel $model
     * @return void
     */
    public function prepareImageToSave(AbstractModel $model)
    {
        foreach ($model->getImagesFields() as $imageField) {
            $imageData = $model->getData($imageField);
            if (is_array($imageData)) {
                foreach ($imageData as $image) {
                    $imageName = '';
                    if (!empty($image['name'])) {
                        $imageName = $this->imageUploader->generateImageName($image['name']);
                    } elseif (!empty($image['file'])) {
                        $imageName = $image['file'];
                    }
                    $model->setData($imageField, $imageName);
                }
                $model->setData($imageField . '_data', $imageData);
            } else {
                $model->setData($imageField, '');
            }
        }
    }

    /**
     * @param AbstractModel $model
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveImages(AbstractModel $model)
    {
        $result = [];
        foreach ($model->getImagesFields() as $imageField) {
            $imageData = $model->getData($imageField . '_data');
            $oldImageName = $model->getData('old_' . $imageField);
            if (is_array($imageData)) {
                foreach ($imageData as $image) {
                    if (!empty($image['name'])) {
                        $this->imageUploader->removeImageDirectory($imageField, $oldImageName);
                        $result[$imageField] = $this->processNewFile(
                            $image['name'],
                            $imageField,
                            $model->getData($imageField)
                        );
                    }
                }
            } else {
                $this->imageUploader->removeImageDirectory($imageField, $oldImageName);
            }
        }
        return $result;
    }
    
    /**
     * @param string $imageName
     * @param string $imageField
     * @param string $newImageName
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function processNewFile($imageName, $imageField, $newImageName)
    {
        return $this->imageUploader->moveFileFromTmp($imageName, $imageField, $newImageName);
    }

    /**
     * @param string $imageName
     * @param string $imageType
     * @return array|null
     */
    public function getImageForUploader($imageName, $imageType)
    {
        if (!empty($imageName)) {
            $path = $this->imageUploader->getImagePath($imageName, $imageType);
            $file = $this->imageUploader->getMediaDirectory()->getAbsolutePath($path);
            if ($this->fileSystemDriver->fileExists($file)) {
                return [
                    [
                        'exists' => true,
                        'file' => $imageName,
                        'url' => $this->imageUploader->getWebUrl(
                            $this->imageUploader->getImagePath($imageName, $imageType)
                        ),
                        'type' => $this->imageUploader->getMimeType($file),
                        'size' => $this->imageUploader->getMediaDirectory()->stat($path)['size']
                    ]
                ];
            }
        }
        return null;
    }

    /**
     * @param $imageFile
     * @param $imageType
     */
    public function getWebUrl($imageFile, $imageType)
    {
        $imagePath = $this->imageUploader->getWebUrl(
            $this->imageUploader->getImagePath($imageFile, $imageType)
        );
        return $imagePath;
    }
}
