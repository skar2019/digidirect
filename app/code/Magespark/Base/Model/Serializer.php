<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  MageSpark
 * @package   MageSpark\Base
 * @author    MageSpark team <support@magespark.com>
 * @copyright 2020 MageSpark
 */

namespace MageSpark\Base\Model;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Unserialize\Unserialize;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Wrapper for Serialize
 * @since 1.1.0
 */
class Serializer
{
    /**
     * @var mixed
     */
    private $serializer;

    /**
     * @var Unserialize
     */
    private $unserialize;

    /**
     * Serializer constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param Unserialize $unserialize
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Unserialize $unserialize
    ) {
        if (interface_exists(SerializerInterface::class)) {
            // for magento later then 2.2
            $this->serializer = $objectManager->get(SerializerInterface::class);
        }
        $this->unserialize = $unserialize;
    }

    /**
     * Serialize the value parameter
     *
     * @param $value
     * @return bool|string
     */
    public function serialize($value)
    {
        if ($this->serializer === null) {
            return serialize($value);
        }

        return $this->serializer->serialize($value);
    }

    /**
     * Unserialize value parameter
     *
     * @param $value
     * @return array|bool|float|int|mixed|null|string
     */
    public function unserialize($value)
    {
        if ($this->serializer === null) {
            return $this->unserialize->unserialize($value);
        }

        try {
            return $this->serializer->unserialize($value);
        } catch (\InvalidArgumentException $exception) {
            return unserialize($value);
        }
    }
}
