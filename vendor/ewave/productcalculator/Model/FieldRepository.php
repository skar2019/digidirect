<?php

namespace Ewave\ProductCalculator\Model;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Ewave\ProductCalculator\Model\Media\ImageProcessorFactory;
use Ewave\ProductCalculator\Api\Data\FieldInterface;
use Ewave\ProductCalculator\Api\FieldRepositoryInterface;

/**
 * Class FieldRepository
 * @package Ewave\ProductCalculator\Model
 */
class FieldRepository implements FieldRepositoryInterface
{
    /**
     * @var FieldFactory
     */
    protected $fieldFactory;

    /**
     * @var ResourceModel\Field
     */
    protected $fieldResource;

    /**
     * @var ImageProcessorFactory
     */
    protected $imageProcessorFactory;

    /**
     * FieldRepository constructor.
     *
     * @param FieldFactory $fieldFactory
     * @param ResourceModel\Field $fieldResource
     * @param ImageProcessorFactory $imageProcessorFactory
     */
    public function __construct(
        FieldFactory $fieldFactory,
        ResourceModel\Field $fieldResource,
        ImageProcessorFactory $imageProcessorFactory
    ) {
        $this->fieldFactory = $fieldFactory;
        $this->fieldResource = $fieldResource;
        $this->imageProcessorFactory = $imageProcessorFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(FieldInterface $field)
    {
        try {
            /** @var Field $field **/
            $this->prepareData($field);
            $images = $this->_processImages($field);
            if (!empty($images)) {
                $this->deleteOldImage($images);
            }
            $field->getResource()->save($field);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save product calculator field: %1',
                $exception->getMessage()
            ));
        }
        return $field;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($fieldId)
    {
        /** @var Field $field **/
        $field = $this->fieldFactory->create();
        $field->getResource()->load($field, $fieldId);
        if (!$field->getId()) {
            throw new NoSuchEntityException(__('Field with id "%1" does not exist.', $fieldId));
        }
        return $field;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(FieldInterface $field)
    {
        try {
            $field->getResource()->delete($field);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete product calculator field: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($fieldId)
    {
        return $this->delete($this->getById($fieldId));
    }

    /**
     * @param FieldInterface $object
     * @return void
     */
    protected function prepareData(FieldInterface $object)
    {
        /** @var Field $object */
        $data = $object->getData();
        if (!$object->getId()) {
            $object->unsetData('id');
        }

        if (isset($data['rule'])) {
            $conditionsArray = $object->getRule()
                ->loadPost($data['rule'])
                ->getConditions()
                ->asArray();

            $object->setConditionsSerialized(serialize($conditionsArray));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updateStatus($ids, $status)
    {
        return $this->fieldResource->updateStatus($ids, $status);
    }

    /**
     * Process option images
     *
     * @param FieldInterface $field
     * @return string[]
     * @throws LocalizedException
     */
    protected function _processImages($field)
    {
        /** @var Field $field **/
        $images = [];

        if ($imageFile = $this->getImageInfo($field)) {
            $result = $this->imageProcessorFactory->create()->save($imageFile);
            //Result can be empty array and it mean that image stay the same
            if (isset($result['error'])) {
                throw new LocalizedException($result['error']);
            } elseif (!empty($result['file'])) {
                // Remember old image and delete earlier
                if ($field->hasData(FieldInterface::IMAGE)
                    && ($img = $field->getData(FieldInterface::IMAGE))
                    && $img != $result['file']) {
                    $images[] = $img;
                }
                $field->setData(FieldInterface::IMAGE, $result['file']);
            }
        } else { // It mean that image was deleted or it wasn't uploaded
            if ($img = $field->getData(FieldInterface::IMAGE)) {
                $images[] = $img;
            }

            $field->setData(FieldInterface::IMAGE, null);
        }

        return $images;
    }

    /**
     * Get image information from field
     *
     * @param FieldInterface $field
     * @return string|bool
     */
    protected function getImageInfo($field)
    {
        /** @var Field $field **/
        $imageInfo = $field->getData(FieldInterface::IMAGE_INFO_KEY);
        if (!empty($imageInfo)) {
            return current($imageInfo);
        }

        return false;
    }

    /**
     * Delete Old Image
     *
     * @param [] $images
     * @return bool
     */
    protected function deleteOldImage($images)
    {
        $imageProcessor = $this->imageProcessorFactory->create();
        if (!empty($images)) {
            if (!is_array($images)) {
                $images = [$images];
            }
            foreach ($images as $image) {
                if (!$image) {
                    continue;
                }
                $imageProcessor->remove($image);
            }

            return true;
        }

        return false;
    }
}
