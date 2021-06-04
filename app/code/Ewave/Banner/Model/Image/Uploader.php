<?php

namespace Ewave\Banner\Model\Image;

use Ewave\Banner\Helper\IssetTrait;
use Magento\MediaStorage\Model\File\UploaderFactory as FileUploaderFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\App\Filesystem\DirectoryList;
use Ewave\Banner\Helper\Image\Config;
use Magento\Framework\Exception\LocalizedException;
use Ewave\Banner\Model\Attributes\ExtensionAttributes\Video;
use Magento\Framework\View\Asset\Repository;
use Magento\Banner\Model\Banner;
use Magento\Framework\Exception\FileSystemException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Uploader
{
    use IssetTrait;

    const TEMPORARY_FILE_POSTFIX = '.tmp';
    const ORIGINAL_UPLOADED_FILE_TMP_PATH = 'banner/original';
    const BANNER_IMAGES_DIR_NAME = 'bw/';

    /**
     * @var Config
     */
    protected $imageConfig;

    /**
     * @var UploaderFactory
     */
    protected $fileUploaderFactory;

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @var AdapterFactory
     */
    protected $imageFactory;

    /**
     * @var File
     */
    protected $file;

    /**
     * @var []
     */
    protected $data;

    /**
     * @var Repository
     */
    protected $assetRepo;

    /**
     * Logger
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Uploader constructor.
     *
     * @param Config $config
     * @param FileUploaderFactory $uploaderFactory
     * @param Filesystem $fileSystem
     * @param AdapterFactory $imageFactory
     * @param File $file
     * @param Repository $repository
     * @param \Psr\Log\LoggerInterface $logger
     * @param array $data
     * @throws FileSystemException
     */
    public function __construct(
        Config $config,
        FileUploaderFactory $uploaderFactory,
        Filesystem $fileSystem,
        AdapterFactory $imageFactory,
        File $file,
        Repository $repository,
        \Psr\Log\LoggerInterface $logger,
        array $data = []
    ) {
        $this->imageConfig = $config;
        $this->fileUploaderFactory = $uploaderFactory;
        $this->fileSystem = $fileSystem;
        $this->imageFactory = $imageFactory;
        $this->file = $file;
        $this->mediaDirectory = $fileSystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->data = $data;
        $this->assetRepo = $repository;
        $this->logger = $logger;
    }

    /**
     * @param string $path
     * @return null
     */
    public function getFileSize($path)
    {
        try {
            $fileHandler = $this->mediaDirectory->stat($this->mediaDirectory->getAbsolutePath($path));
            $size = $fileHandler['size'];
        } catch (FileSystemException $e) {
            $this->logger->warning($e);
            $size = null;
        }
        return $size;
    }

    /**
     * Upload full size image
     *
     * @param string $file
     * @return bool
     */
    public function uploadOriginalImage($file)
    {
        $uploaded = false;
        if (strpos($file, self::TEMPORARY_FILE_POSTFIX) !== false) {
            $pathParts = explode('/', $file);
            $file = $this->_removeTemporaryFilePostfix($file);

            $uploaded = $this->imageResize(
                $this->mediaDirectory->getAbsolutePath('tmp/' . self::ORIGINAL_UPLOADED_FILE_TMP_PATH) . $file,
                $this->mediaDirectory->getAbsolutePath(self::BANNER_IMAGES_DIR_NAME . 'original/')
                . $this->_removeTemporaryFilePostfix(end($pathParts)),
                null,
                null
            );

            if ($uploaded) {
                return str_replace(self::TEMPORARY_FILE_POSTFIX, '', end($pathParts));
            }
        }
        return $uploaded;
    }

    /**
     * Remove postfix
     *
     * @param string $fileName
     * @return string
     */
    protected function _removeTemporaryFilePostfix($fileName)
    {
        return str_replace(self::TEMPORARY_FILE_POSTFIX, '', $fileName);
    }

    /**
     * Resize image
     *
     * @param string $absolutePath
     * @param string $imageResized
     * @param string $height
     * @param string $width
     * @return bool
     */
    public function imageResize($absolutePath, $imageResized, $height, $width)
    {
        if (!$this->file->isExists($absolutePath)) {
            return false;
        }

        $imageResize = $this->imageFactory->create();
        $imageResize->open($absolutePath);
        $imageResize->constrainOnly(false);
        $imageResize->keepTransparency(true);
        $imageResize->keepFrame(true);
        $imageResize->keepAspectRatio(true);
        $imageResize->backgroundColor([255, 255, 255]);

        if ($width !== null && $height !== null) {
            $imageResize->resize($width, $height);
        }
        $dest = $imageResized;
        $imageResize->save($dest);
        return (bool)$imageResize;
    }

    /**
     * Resize Image
     *
     * @param string $absolutePath
     * @param string $role
     * @return bool
     */
    public function makeResize($absolutePath, $role)
    {
        $path = $role;
        if (!$path) {
            return false;
        }
        $imageSize = $this->imageConfig->getImageSizeBySet($role);
        $imageHeight = $imageSize['height'];
        $imageWidth = $imageSize['width'];

        $pathToFile = $this->mediaDirectory->getAbsolutePath(static::BANNER_IMAGES_DIR_NAME . 'original')
            . '/' . $absolutePath;
        $path = $this->mediaDirectory->getAbsolutePath(static::BANNER_IMAGES_DIR_NAME . $role)
            . '/' . $absolutePath;

        $uploaded = $this->imageResize($pathToFile, $path, $imageHeight, $imageWidth);
        if ($uploaded) {
            return self::BANNER_IMAGES_DIR_NAME . $role . '/' . $absolutePath;
        }

        return $uploaded;
    }

    /**
     * Delete image
     *
     * @param string $imagePath
     * @return $this
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function deleteImage($imagePath)
    {
        if ($imagePath) {
            $imagePath = $this->mediaDirectory->getAbsolutePath($imagePath);
            if ($this->file->isExists($imagePath)) {
                $this->file->deleteFile($imagePath);
            }
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultRole()
    {
        return $this->imageConfig->getDefaultSrcSetName();
    }

    /**
     * Upload image
     *
     * @param string $fileId
     * @return array
     * @throws LocalizedException
     */
    public function uploadImage($fileId = 'image')
    {
        /**
         * @var $uploader \Magento\MediaStorage\Model\File\Uploade
         */
        $uploader = $this->fileUploaderFactory->create(['fileId' => $fileId]);
        $uploader->setAllowedExtensions($this->imageConfig->getAllowedExtensions());
        $uploader->addValidateCallback('banner_image_gallery', $this->imageFactory, 'validateUploadFile');
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(true);
        if (!$uploader->checkMimeType(explode(',', $this->imageConfig->getAllowedImagesMimeTypes()))) {
            throw new LocalizedException(__('Invalid file mime type'));
        }
        return $uploader->save($this->mediaDirectory->getAbsolutePath($this->imageConfig->getBaseTmpMediaPath()));
    }

    /**
     * Upload image
     *
     * @return array
     * @throws LocalizedException
     */
    public function uploadVideo()
    {
        $uploader = $this->fileUploaderFactory->create(['fileId' => 'image']);
        $allowedTypes = $this->imageConfig->getAllowedExtensions();
        $allowedTypes = array_merge($allowedTypes, $this->getByKey($this->data, 'allowed_video_types', []));
        $uploader->setAllowedExtensions($allowedTypes);
        /** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter */
        $uploader->addValidateCallback('banner_image_gallery', $this->imageFactory, 'validateUploadFile');
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(true);

        return $uploader->save($this->mediaDirectory->getAbsolutePath($this->imageConfig->getBaseTmpMediaPath()));
    }

    /**
     * @param [] $result
     * @return array
     */
    public function prepareVideoResult(array $result = [])
    {
        $result = $this->prepareResult($result);
        $result['url'] = $this->assetRepo->getUrl('Ewave_Banner::images/placeholder.jpg');
        return $result;
    }

    /**
     * @param array $result
     * @return array
     */
    public function prepareResult(array $result = [])
    {
        unset($result['tmp_name']);
        unset($result['path']);

        $result['url'] = $this->imageConfig->getTmpMediaUrl($result['file']);
        $result['file'] = $result['file'] . \Ewave\Banner\Model\Image\Uploader::TEMPORARY_FILE_POSTFIX;
        return $result;
    }

    /**
     * @param [] $bannerImages
     * @param Banner $model
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @return void
     */
    public function uploadImages($bannerImages, Banner $model)
    {
        if (isset($bannerImages[Config::IMAGES_UPLOADED_IMAGES_VAR_NAME])
            && is_array($bannerImages[Config::IMAGES_UPLOADED_IMAGES_VAR_NAME])
        ) {
            $deletedImages = [];
            $images = [];
            $this->validateVideoLimit($bannerImages[Config::IMAGES_UPLOADED_IMAGES_VAR_NAME]);

            foreach ($bannerImages[Config::IMAGES_UPLOADED_IMAGES_VAR_NAME] as $key => $bannerImage) {
                if (isset($bannerImage['media_type']) && $bannerImage['media_type'] == 'external-video') {
                    $this->processVideo($model, $bannerImage, $key);
                    continue;
                }

                if (isset($bannerImage['removed']) && !empty($bannerImage['removed'])) {
                    $this->deleteImage($bannerImage['file']);
                    $deletedImages[$key] = !empty($bannerImage['value_id']) ? $bannerImage['value_id'] : $key;
                }

                $uploaded = $this->uploadOriginalImage($bannerImage['file']);
                if ($uploaded && $bannerImage['roles'] == '') {
                    $bannerImage['roles'] = [$key];
                }

                if ($uploaded) {
                    $fileToResize = $uploaded;
                    $bannerImageRoles = $this->getByKey($bannerImage, 'roles', '');
                    $rolesArray = is_array($bannerImageRoles) ? $bannerImageRoles : explode(',', $bannerImageRoles);
                    foreach ($rolesArray as $role) {
                        $uploaded = $this->makeResize(
                            $fileToResize,
                            $role
                        );
                        $images[$role] = $uploaded;
                    }
                    $model->setData('uploaded_images', $images);
                }
                $deletedImagesData = $model->getData('deleted_images');
                if (!is_array($deletedImagesData)) {
                    $deletedImagesData = [];
                }

                $deletedImages = array_merge($deletedImagesData, $deletedImages);
                $model->setData('deleted_images', $deletedImages);

                if ($this->isRoleRemoved($bannerImage)) {
                    $this->removeImageRole($bannerImage, $model, $images, $key);
                }

                if ($this->isRoleChanged($bannerImage)) {
                    $this->changeImageRole($bannerImage, $model, $images);
                }
            }
        }
    }

    /**
     * @param [] $bannerImages
     * @throws LocalizedException
     * @return void
     */
    protected function validateVideoLimit($bannerImages)
    {
        $qty = 0;
        $limit = 1;
        foreach ($bannerImages as $item) {
            if (isset($item['removed']) && $item['removed'] || ($item['media_type'] !== 'external-video')) {
                continue;
            }
            $qty++;
        }
        if ($qty > $limit) {
            throw new LocalizedException(
                __('Max video limit equal to %1. Please, remove extra video(s) and try to save again', $limit)
            );
        }
    }

    /**
     * @param string $filename
     * @param bool $isTmp
     * @return string
     */
    protected function getUploadedTmpFile($filename, $isTmp = false)
    {
        if ($isTmp) {
            $filename = $this->_removeTemporaryFilePostfix($filename);
        }
        return $this->mediaDirectory->getRelativePath('tmp/' . self::ORIGINAL_UPLOADED_FILE_TMP_PATH . $filename);
    }

    /**
     * @param Banner $banner
     * @param [] $image
     * @param string $imageId
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function processVideo(Banner $banner, $image, $imageId)
    {
        $imageId = $this->getVideoId($image, $imageId);

        $uploadedVideos = $banner->getData('uploaded_videos');
        $deletedVideos = $banner->getData('deleted_videos');
        if (!is_array($deletedVideos)) {
            $deletedVideos = [];
        }

        if (!is_array($uploadedVideos)) {
            $uploadedVideos = [];
        }

        if (isset($image['removed']) && !empty($image['removed'])) {
            $this->deleteImage($image['file']);
            $deletedVideos[$imageId] = $image['value_id'];
            $banner->setData('deleted_videos', $deletedVideos);
            return;
        }
        if (!empty($banner->getOrigData())) {
            $this->deleteChangedVideos($banner, $image);
        }

        $videoTmp = $this->getArrayProperty($image, Video::VIDEO_FILE_KEY);

        if ($videoTmp) {
            $pathParts = explode('/', $videoTmp);
            $fileName = end($pathParts);
            $fileNameWithoutPostfix = $this->_removeTemporaryFilePostfix($fileName);
            $uploadedTmpFile = $this->getUploadedTmpFile($videoTmp, true);
            if ($this->mediaDirectory->isExist($uploadedTmpFile)) {
                $destination = $this->mediaDirectory->getRelativePath(static::BANNER_IMAGES_DIR_NAME)
                    . $imageId . '/' . $fileNameWithoutPostfix;
                $uploadedVideo = $this->mediaDirectory->copyFile($uploadedTmpFile, $destination);

                if (!$uploadedVideo) {
                    return;
                }
            }

            $uploadedVideos[$imageId] = [
                'path' => static::BANNER_IMAGES_DIR_NAME . $imageId . '/' . $fileNameWithoutPostfix,
            ];
        }
        $imageUploadedTmp = $image['file'];
        if ($imageUploadedTmp) {
            $imageUploadedPath = explode('/', $imageUploadedTmp);
            $uploadedImageWithoutPostfix = $this->_removeTemporaryFilePostfix(end($imageUploadedPath));

            $previewImagePath = $this->mediaDirectory->getAbsolutePath(
                self::BANNER_IMAGES_DIR_NAME . $imageId . '/preview'
            );

            $previewImage = null;
            if ($this->mediaDirectory->isExist($this->getUploadedTmpFile($imageUploadedTmp, true))) {
                try {
                    $path = $this->mediaDirectory->getAbsolutePath(
                        $this->getUploadedTmpFile($imageUploadedTmp, true)
                    );
                    $previewImage = $this->imageResize(
                        $path,
                        $previewImagePath . '/' . $uploadedImageWithoutPostfix,
                        null,
                        null
                    );
                } catch (\Exception $e) {
                    $previewImage = null;
                }
            }
            if ($previewImage) {
                $uploadedVideos[$imageId]['preview_image'] = self::BANNER_IMAGES_DIR_NAME . $imageId
                    . '/preview/' . $uploadedImageWithoutPostfix;
            } else {
                $uploadedVideos[$imageId]['preview_image'] = $this->_removeTemporaryFilePostfix($image['file']);
            }
        }
        $uploadedVideos[$imageId] = array_merge($uploadedVideos[$imageId], $image);
        $uploadedVideos[$imageId]['video_folder'] = $imageId;

        $banner->setData('uploaded_videos', $uploadedVideos);
    }

    /**
     * @param Banner $banner
     * @param [] $image
     * @return $this
     */
    protected function deleteChangedVideos($banner, $image)
    {
        foreach ($banner->getImagesBanner() as $savedImage) {
            if ($savedImage['value_id'] !== $image['value_id'] || $savedImage['video_file'] == $image['video_file']) {
                continue;
            }
            $this->deleteImage($savedImage['video_file']);
        }
        return $this;
    }

    /**
     * @param [] $image
     * @param string $imageId
     * @return string
     */
    protected function getVideoId($image, $imageId)
    {
        return isset($image['video_folder']) && !empty($image['video_folder']) ? $image['video_folder'] : $imageId;
    }

    /**
     * @param [] $array
     * @param string $key
     * @return null
     */
    protected function getArrayProperty($array, $key)
    {
        return $this->issetAndNotEmpty($array, $key) ? $array[$key] : null;
    }

    /**
     * If role changed:
     * 1) get old role and mark as deleted
     * 2) get file name
     * 3) get original image and resize it to new role's size
     * 4) set new image as uploaded
     * 5) set new image in images array by reference if image was before other images
     *
     * @param [] $bannerImage
     * @param Banner $model
     * @param [] $images
     * @return void
     */
    protected function changeImageRole($bannerImage, Banner $model, &$images)
    {
        $oldValue = $this->issetAndNotEmpty($bannerImage, 'value_id') ? $bannerImage['value_id'] : null;
        $imageRole = $this->issetAndNotEmpty($bannerImage, 'roles') ? $bannerImage['roles'] : null;
        $fileAsArray = $this->issetAndNotEmpty($bannerImage, 'file') ? explode('/', $bannerImage['file']) : [];
        $fileName = end($fileAsArray);
        $uploaded = $this->makeResize($fileName, $imageRole);
        $images[$imageRole] = $uploaded;
        $deletedImages = $model->getData('deleted_images');
        $deletedImages[$oldValue] = $oldValue;
        $uploadedImages = $model->getData('uploaded_images');
        $uploadedImages[$imageRole] = $uploaded;
        $model->setData('deleted_images', $deletedImages);
        $model->setData('uploaded_images', $uploadedImages);
    }

    /**
     * @param [] $bannerImage
     * @param Banner $model
     * @param [] $images
     * @param string $role
     * @return void
     */
    protected function removeImageRole(&$bannerImage, Banner $model, &$images, $role)
    {
        $bannerImage['roles'] = $role;
        $this->changeImageRole($bannerImage, $model, $images);
        $bannerImage['roles'] = null;
    }

    /**
     * Get image array
     * check old role and new role and compare it
     *
     * @param [] $bannerImage
     * @return bool
     */
    protected function isRoleChanged($bannerImage)
    {
        $changed = false;
        $oldValue = $this->issetAndNotEmpty($bannerImage, 'value_id') ? $bannerImage['value_id'] : null;
        $imageRole = $this->issetAndNotEmpty($bannerImage, 'roles') ? $bannerImage['roles'] : null;

        if (null !== $oldValue && null !== $imageRole && $imageRole !== $oldValue) {
            $changed = true;
        }

        return $changed;
    }

    /**
     * @param [] $bannerImage
     * @return bool
     */
    protected function isRoleRemoved($bannerImage)
    {
        $imageRole = $this->issetAndNotEmpty($bannerImage, 'roles') ? $bannerImage['roles'] : null;
        return $imageRole == 'removed';
    }

    /**
     * Helper function
     *
     * @param [] $array
     * @param string $key
     * @return bool
     */
    protected function issetAndNotEmpty($array, $key)
    {
        return isset($array[$key]) && !empty($array[$key]);
    }
}
