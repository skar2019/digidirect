<?php

namespace Ewave\Banner\Model\Attributes;

use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\UrlInterface;
use Magento\Banner\Model\Banner as BannerModel;
use Ewave\Banner\Model\Upload\ImageProcessor;

class NavigationTitle implements AttributesInterface
{
    const ATTRIBUTE_CODE = 'navigation_title';
    const NAVIGATION_TYPE_TITLE_WYSIWYG = 1;
    const NAVIGATION_TYPE_TITLE_IMAGE = 2;

    /**
     * @var FilterProvider
     */
    protected $filterProvider;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var ImageProcessor
     */
    protected $imageProcessor;

    /**
     * NavigationTitle constructor.
     * @param FilterProvider $filterProvider
     * @param UrlInterface $url
     * @param ImageProcessor $imageProcessor
     */
    public function __construct(
        FilterProvider $filterProvider,
        UrlInterface $url,
        ImageProcessor $imageProcessor
    ) {
        $this->filterProvider = $filterProvider;
        $this->urlBuilder = $url;
        $this->imageProcessor = $imageProcessor;
    }

    /**
     * @param BannerModel $banner
     * @return string
     */
    public function getFrontendAttribute(BannerModel $banner)
    {
        $customAttributes = $banner->getCustomAttributes();
        $navigationTitle = isset($customAttributes[self::ATTRIBUTE_CODE]) ?
            $customAttributes[self::ATTRIBUTE_CODE] : '';
        if (isset($customAttributes['navigation_title_type'])
            && $customAttributes['navigation_title_type'] == NavigationTitle::NAVIGATION_TYPE_TITLE_IMAGE
        ) {
            $customAttributes['image'] = true;
            $banner->setData('custom_attributes', $customAttributes);
            if (empty($customAttributes['navigation_image'])) {
                return null;
            }
            return $this->imageProcessor->getWebUrl(
                $customAttributes['navigation_image'],
                NavigationImage::ATTRIBUTE_CODE
            );
        }
        $customAttributes['image'] = false;
        $banner->setData('custom_attributes', $customAttributes);
        return $this->filterProvider->getPageFilter()->filter($navigationTitle);
    }

    /**
     * @param BannerModel $banner
     * @return mixed
     */
    public function setAttribute(BannerModel $banner)
    {
        return $banner->getData(self::ATTRIBUTE_CODE);
    }

    /**
     * @return array
     */
    public function getNavigationTypes()
    {
        return [
            self::NAVIGATION_TYPE_TITLE_WYSIWYG => __('Title'),
            self::NAVIGATION_TYPE_TITLE_IMAGE => __('Image'),
        ];
    }
}
