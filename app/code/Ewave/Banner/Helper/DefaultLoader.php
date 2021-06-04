<?php

namespace Ewave\Banner\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\View\Asset\Repository;

class DefaultLoader extends AbstractHelper
{
    /**
     * @var Repository
     */
    private $assetRepo;

    /**
     * Data constructor.
     * @param Context $context
     * @param Repository $repository
     */
    public function __construct(Context $context, Repository $repository)
    {
        parent::__construct($context);
        $this->assetRepo = $repository;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        try {
            return $this->assetRepo->getUrlWithParams('images/loader-2.gif', []);
        } catch (\Throwable $exception) {
            return '';
        }
    }
}
