<?php
namespace Ewave\GiftCardImage\Model\Source;

use Ewave\GiftCardImage\Api\GiftCardImageRepositoryInterface;
use Ewave\GiftCardImage\Api\Data\GiftCardImageInterface;
use Ewave\GiftCardImage\Model\GiftCardImageRepository;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class GiftCardImages extends AbstractSource
{
    /**
     * @var GiftCardImageRepositoryInterface|GiftCardImageRepository
     */
    protected $giftCardImageRepository;

    /**
     * GiftCardImages constructor.
     * @param GiftCardImageRepositoryInterface $giftCardImageRepository
     */
    public function __construct(
        GiftCardImageRepositoryInterface $giftCardImageRepository
    ) {
        $this->giftCardImageRepository = $giftCardImageRepository;
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $result = [];
        foreach ($this->_getValues() as $k => $v) {
            $result[] = ['value' => (string)$k, 'label' => $v];
        }
        return $result;
    }

    /**
     * Get option text
     *
     * @param int|string $value
     * @return null|string
     */
    public function getOptionText($value)
    {
        $options = $this->_getValues();
        if (isset($options[$value])) {
            return $options[$value];
        }
        return null;
    }

    /**
     * Get values
     *
     * @return array
     */
    protected function _getValues()
    {
        $values = [];
        $collection = $this->giftCardImageRepository->getActiveGiftcardImages();
        foreach ($collection as $item) {
            /** @var GiftCardImageInterface $item */
            $values[$item->getId()] = $item->getTitle();
        }
        return $values;
    }
}
