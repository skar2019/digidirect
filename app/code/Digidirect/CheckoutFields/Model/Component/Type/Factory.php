<?php
// @codingStandardsIgnoreFile

/**
 * Model factory
 */
namespace Digidirect\CheckoutFields\Model\Component\Type;

use \Magento\Framework\Api\ObjectFactory;
use \Digidirect\CheckoutFields\Model\Component\Type\AbstractType;
use \Magento\Framework\Exception\LocalizedException;

/**
 * Class Factory
 * @package Digidirect\CheckoutFields\Model\Component\Type
 */
class Factory
{
    /**
     * Object Factory
     *
     * @var ObjectFactory
     */
    protected $objectFactory;

    /**
     * Construct
     *
     * @param ObjectFactory $objectFactory
     */
    public function __construct(ObjectFactory $objectFactory)
    {
        $this->objectFactory = $objectFactory;
    }

    /**
     * Create model
     *
     * @param string $className
     * @param array $data
     * @return \Magento\Framework\Model\AbstractModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function create($className, array $data = [])
    {
        $model = $this->objectFactory->create(ltrim($className, '\\'), $data);
        if (!$model instanceof AbstractType) {
            throw new LocalizedException(
                __('%1 doesn\'t extends \Digidirect\CheckoutFields\Model\Component\Type\AbstractType', $className)
            );
        }
        return $model;
    }
}
