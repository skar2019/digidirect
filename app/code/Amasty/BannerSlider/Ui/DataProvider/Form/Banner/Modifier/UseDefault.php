<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Ui\DataProvider\Form\Banner\Modifier;

use Amasty\BannerSlider\Api\Data\BannerInterface;
use Amasty\BannerSlider\Api\BannerRepositoryInterface;
use Magento\Store\Model\Store;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Framework\Registry;
use Magento\Framework\App\RequestInterface;

class UseDefault implements ModifierInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $storeId;

    /**
     * @var BannerRepositoryInterface
     */
    private $bannerRepository;

    public function __construct(
        Registry $registry,
        RequestInterface $request,
        BannerRepositoryInterface $bannerRepository
    ) {
        $this->bannerRepository = $bannerRepository;
        $this->storeId = (int)$request->getParam('store', Store::DEFAULT_STORE_ID);
        $this->id = $registry->registry(BannerInterface::PERSIST_NAME)->getId();
    }

    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta)
    {
        if ($this->storeId && $this->id) {
            foreach (BannerInterface::STATIC_FIELDS as $field) {
                $meta['general']['children'][$field]['arguments']['data']['config']['disabled'] = true;
            }
        }

        return $meta;
    }

    /**
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        return $data;
    }
}
