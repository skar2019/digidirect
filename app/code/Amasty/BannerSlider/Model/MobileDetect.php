<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model;

use Magento\Framework\ObjectManagerInterface;

class MobileDetect
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Detection\MobileDetect|null
     */
    private $mobileDetector = null;

    /**
     * @var \Magento\Framework\HTTP\Header
     */
    private $httpHeader;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    public function __construct(
        \Magento\Framework\HTTP\Header $httpHeader,
        \Magento\Framework\App\RequestInterface $request,
        ObjectManagerInterface $objectManager
    ) {
        $this->httpHeader = $httpHeader;
        $this->request = $request;
        $this->objectManager = $objectManager;

        // We are using object manager to create 3rd-party packages' class
        if (class_exists(\Detection\MobileDetect::class)) {
            $this->mobileDetector = $this->objectManager->create(\Detection\MobileDetect::class);
        }
    }

    public function isMobile(): bool
    {
        return $this->mobileDetector === null
            ? stristr($this->httpHeader->getHttpUserAgent(), 'mobi') !== false
            : $this->mobileDetector->isMobile();
    }
}
