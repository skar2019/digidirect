<?php
namespace Ewave\GiftCardImage\Block\Adminhtml\Edit;

use Ewave\GiftCardImage\Api\GiftCardImageRepositoryInterface;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class GenericButton
 */
class GenericButton
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var GiftCardImageRepositoryInterface
     */
    protected $giftCardImageRepository;

    /**
     * GenericButton constructor.
     * @param Context $context
     * @param GiftCardImageRepositoryInterface $giftCardImageRepository
     */
    public function __construct(
        Context $context,
        GiftCardImageRepositoryInterface $giftCardImageRepository
    ) {
        $this->context = $context;
        $this->giftCardImageRepository = $giftCardImageRepository;
    }

    /**
     * @return int|null
     */
    public function getGiftCardImageId()
    {
        try {
            return $this->giftCardImageRepository->getById(
                $this->context->getRequest()->getParam('giftcard_image_id')
            )->getId();
        } catch (NoSuchEntityException $e) {
        }
        return null;
    }

    /**
     * Generate url by route and parameters
     * @param  string $route
     * @param  array $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
