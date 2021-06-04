<?php

namespace Ewave\SEO\Model\Hreflang;

use Magento\Framework\ObjectManagerInterface;
use Ewave\SEO\Model\Hreflang\StoreCodeName;

/**
 * Class HreflangFactory
 */
class HreflangFactory
{
    /**
     *
     */
    const MAIN_OBJECT_PATH = '\\Ewave\\SEO\\Model\\Hreflang\\';

    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * HreflangFactory constructor.
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * @param $objectName
     * @return mixed|null
     */
    public function create($objectName)
    {
        $className = self::MAIN_OBJECT_PATH.$objectName;

        if (class_exists($className)) {
            return $this->objectManager->create($className);
        }

        return null;
    }
}
